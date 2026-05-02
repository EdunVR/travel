// Chat Panel Alpine.js Component
document.addEventListener("alpine:init", () => {
    console.log("✅ Chat Panel Alpine component initializing...");

    Alpine.data("chatPanel", () => ({
        // State
        isOpen: false,
        mode: "superadmin",
        messages: [],
        messageInput: "",
        isTyping: false,
        isSending: false,
        isLoading: false,
        isLoadingUsers: false,
        selectedUserId: null,
        selectedUser: null,
        users: [],
        currentPage: 1,
        hasMoreMessages: false,
        connectionStatus: "online", // Default online
        messageQueue: [],
        errorMessage: "",
        showUserList: true,
        chatbotTab: "messages",
        soundEnabled: true,

        // Computed
        get isSuperadmin() {
            return window.chatConfig?.isSuperadmin || false;
        },

        get currentUserId() {
            return window.chatConfig?.currentUserId || null;
        },

        get characterCount() {
            return this.messageInput.length;
        },

        get canSend() {
            const trimmedLength = this.messageInput.trim().length;
            return (
                trimmedLength > 0 && trimmedLength <= 1000 && !this.isSending
                // Removed offline check - allow sending offline (will queue)
            );
        },

        get validationError() {
            const trimmedLength = this.messageInput.trim().length;

            if (this.messageInput.length > 0 && trimmedLength === 0) {
                return "Pesan tidak boleh hanya berisi spasi.";
            }

            if (this.messageInput.length > 1000) {
                return "Pesan terlalu panjang. Maksimal 1000 karakter.";
            }

            // Removed offline validation - allow typing offline
            // Message will be queued and sent when online

            return "";
        },

        // Initialization
        init() {
            console.log("✅ Chat Panel init() called");
            console.log("Initial state:", {
                connectionStatus: this.connectionStatus,
                isSending: this.isSending,
                isSuperadmin: this.isSuperadmin,
                currentUserId: this.currentUserId,
            });

            // Load sound preference from localStorage
            const savedSoundPref = localStorage.getItem("chat_sound_enabled");
            if (savedSoundPref !== null) {
                this.soundEnabled = savedSoundPref === "true";
            }

            // Request browser notification permission
            this.requestNotificationPermission();

            // Listen for events
            window.addEventListener("chat-panel-open", () => this.openPanel());
            window.addEventListener("chat-panel-close", () =>
                this.closePanel()
            );
            window.addEventListener("chat-message-received", (e) =>
                this.handleIncomingMessage(e.detail)
            );

            // WebSocket connection status (for real-time updates only)
            // Note: WebSocket disconnect doesn't mean offline - HTTP still works
            if (window.Echo?.connector?.pusher) {
                const pusher = window.Echo.connector.pusher;
                pusher.connection.bind("disconnected", () => {
                    console.log("⚠️ WebSocket disconnected (HTTP still works)");
                    // Don't set offline - HTTP requests still work
                });
                pusher.connection.bind("connected", () => {
                    console.log("✅ WebSocket connected");
                    this.sendQueuedMessages();
                });
                pusher.connection.bind("unavailable", () => {
                    console.log("⚠️ WebSocket unavailable (HTTP still works)");
                    // Don't set offline - HTTP requests still work
                });
                pusher.connection.bind("failed", () => {
                    console.log("⚠️ WebSocket failed (HTTP still works)");
                    // Don't set offline - HTTP requests still work
                });
            } else {
                console.log(
                    "ℹ️ WebSocket not configured - using HTTP only mode"
                );
            }

            // Browser online/offline events (actual network status)
            window.addEventListener("online", () => {
                console.log("✅ Browser online");
                this.connectionStatus = "online";
                this.sendQueuedMessages();
            });
            window.addEventListener("offline", () => {
                console.log("❌ Browser offline");
                this.connectionStatus = "offline";
            });

            // Watch message input
            this.$watch("messageInput", () => {
                this.errorMessage = "";
            });
        },

        // Panel Management
        async openPanel() {
            this.isOpen = true;

            // Clear notification badge
            window.dispatchEvent(
                new CustomEvent("chat-unread-updated", {
                    detail: { count: 0 },
                })
            );

            // Load data
            if (this.isSuperadmin) {
                await this.loadUserList();
            } else {
                await this.loadMessages();
            }

            // Mark as read
            setTimeout(() => this.markAsRead(), 500);

            // Focus input
            setTimeout(() => this.$refs.messageInput?.focus(), 100);
        },

        closePanel() {
            this.isOpen = false;
            window.dispatchEvent(new CustomEvent("chat-panel-closed"));
        },

        // Mode Switching
        async switchMode(newMode) {
            if (this.mode === newMode) return;

            this.mode = newMode;
            this.messages = [];
            this.currentPage = 1;
            this.errorMessage = "";
            this.chatbotTab = "messages";

            if (this.isSuperadmin) {
                await this.loadUserList();
            } else {
                await this.loadMessages();
            }
        },

        async switchChatbotTab(tab) {
            if (this.chatbotTab === tab) return;

            this.chatbotTab = tab;
            this.messages = [];
            this.currentPage = 1;
            this.errorMessage = "";

            await this.loadMessages();
        },

        // User List
        async loadUserList() {
            if (!this.isSuperadmin) return;

            this.isLoadingUsers = true;
            this.errorMessage = "";

            try {
                const url =
                    window.chatConfig?.routes?.users || "/admin/chat/users";
                const response = await fetch(url, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                });

                if (!response.ok) throw new Error("Failed to load users");

                const data = await response.json();
                this.users = data.users || [];

                // Auto-select first user
                if (this.users.length > 0 && !this.selectedUserId) {
                    this.selectUser(this.users[0]);
                }
            } catch (error) {
                console.error("Failed to load users:", error);
                this.errorMessage = "Gagal memuat daftar pengguna.";
            } finally {
                this.isLoadingUsers = false;
            }
        },

        async selectUser(user) {
            if (!this.isSuperadmin) return;

            this.selectedUserId = user.id;
            this.selectedUser = user;
            this.messages = [];
            this.currentPage = 1;
            this.errorMessage = "";

            await this.loadMessages();
        },

        // Messages
        async loadMessages(page = 1) {
            if (this.isLoading) return;

            this.isLoading = true;
            this.errorMessage = "";

            try {
                let messageMode = this.mode;
                if (this.isSuperadmin && this.chatbotTab === "chatbot") {
                    messageMode = "chatbot";
                } else if (
                    this.isSuperadmin &&
                    this.chatbotTab === "messages"
                ) {
                    messageMode = "superadmin";
                }

                const params = new URLSearchParams({
                    mode: messageMode,
                    page: page,
                });

                if (this.isSuperadmin && this.selectedUserId) {
                    params.append("user_id", this.selectedUserId);
                }

                const url =
                    window.chatConfig?.routes?.messages ||
                    "/admin/chat/messages";
                const response = await fetch(`${url}?${params}`, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                });

                if (!response.ok) throw new Error("Failed to load messages");

                const data = await response.json();

                if (page === 1) {
                    this.messages = data.messages || [];
                } else {
                    this.messages = [
                        ...(data.messages || []),
                        ...this.messages,
                    ];
                }

                this.hasMoreMessages = data.has_more || false;
                this.currentPage = page;

                if (page === 1) {
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (error) {
                console.error("Failed to load messages:", error);

                const isNetworkError =
                    error.message === "Failed to fetch" || !navigator.onLine;

                if (isNetworkError) {
                    this.errorMessage =
                        "Tidak ada koneksi. Pesan akan dimuat saat koneksi kembali.";
                    this.connectionStatus = "offline";
                } else {
                    this.errorMessage =
                        "Gagal memuat pesan. Silakan coba lagi.";
                }
            } finally {
                this.isLoading = false;
            }
        },

        async loadMoreMessages() {
            if (!this.hasMoreMessages || this.isLoading) return;

            const scrollContainer = this.$refs.messageList;
            const oldScrollHeight = scrollContainer.scrollHeight;

            await this.loadMessages(this.currentPage + 1);

            this.$nextTick(() => {
                const newScrollHeight = scrollContainer.scrollHeight;
                scrollContainer.scrollTop = newScrollHeight - oldScrollHeight;
            });
        },

        // Send Message
        async sendMessage() {
            if (this.validationError) {
                this.errorMessage = this.validationError;
                return;
            }

            if (!this.canSend) return;

            const content = this.messageInput.trim();
            if (!content) {
                this.errorMessage = "Pesan tidak boleh kosong.";
                return;
            }

            if (content.length > 1000) {
                this.errorMessage =
                    "Pesan terlalu panjang. Maksimal 1000 karakter.";
                return;
            }

            this.isSending = true;
            this.errorMessage = "";

            const messageData = {
                content: content,
                mode: this.mode,
                receiver_id:
                    this.isSuperadmin && this.selectedUserId
                        ? this.selectedUserId
                        : null,
            };

            try {
                const url =
                    window.chatConfig?.routes?.sendMessage ||
                    "/admin/chat/messages";
                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                    body: JSON.stringify(messageData),
                });

                if (response.status === 429) {
                    const errorData = await response.json();
                    this.errorMessage =
                        errorData.error ||
                        "Terlalu banyak pesan dikirim. Silakan tunggu sebentar.";
                    return;
                }

                if (response.status === 422) {
                    const errorData = await response.json();
                    this.errorMessage =
                        errorData.details ||
                        errorData.error ||
                        "Data tidak valid.";
                    return;
                }

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(
                        errorData.details ||
                            errorData.error ||
                            "Failed to send message"
                    );
                }

                const data = await response.json();
                this.messages.push(data.message);
                this.messageInput = "";

                this.$nextTick(() => this.scrollToBottom());

                if (this.$refs.messageInput) {
                    this.$refs.messageInput.style.height = "auto";
                }
            } catch (error) {
                console.error("Failed to send message:", error);

                const isNetworkError =
                    error.message === "Failed to fetch" || !navigator.onLine;

                if (this.connectionStatus === "offline" || isNetworkError) {
                    this.messageQueue.push({
                        ...messageData,
                        timestamp: Date.now(),
                        retryCount: 0,
                    });
                    this.errorMessage =
                        "Tidak ada koneksi. Pesan akan dikirim otomatis saat koneksi kembali.";
                    this.connectionStatus = "offline";
                } else {
                    this.errorMessage =
                        error.message ||
                        "Gagal mengirim pesan. Silakan coba lagi.";
                }
            } finally {
                this.isSending = false;
            }
        },

        async sendQueuedMessages() {
            if (this.messageQueue.length === 0) return;

            const queue = [...this.messageQueue];
            this.messageQueue = [];

            let successCount = 0;
            let failedMessages = [];

            for (const messageData of queue) {
                if (messageData.retryCount >= 3) continue;

                try {
                    const url =
                        window.chatConfig?.routes?.sendMessage ||
                        "/admin/chat/messages";
                    const response = await fetch(url, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                            "X-Requested-With": "XMLHttpRequest",
                            Accept: "application/json",
                        },
                        body: JSON.stringify({
                            content: messageData.content,
                            mode: messageData.mode,
                            receiver_id: messageData.receiver_id,
                        }),
                    });

                    if (response.ok) {
                        const data = await response.json();
                        this.messages.push(data.message);
                        successCount++;
                        this.$nextTick(() => this.scrollToBottom());
                    } else {
                        failedMessages.push({
                            ...messageData,
                            retryCount: (messageData.retryCount || 0) + 1,
                        });
                    }
                } catch (error) {
                    failedMessages.push({
                        ...messageData,
                        retryCount: (messageData.retryCount || 0) + 1,
                    });
                }
            }

            this.messageQueue = [...failedMessages, ...this.messageQueue];

            if (this.messageQueue.length === 0 && successCount > 0) {
                const tempMessage = `${successCount} pesan berhasil dikirim.`;
                this.errorMessage = tempMessage;
                setTimeout(() => {
                    if (this.errorMessage === tempMessage) {
                        this.errorMessage = "";
                    }
                }, 3000);
            } else if (this.messageQueue.length > 0) {
                this.errorMessage = `${this.messageQueue.length} pesan menunggu untuk dikirim.`;
            }
        },

        handleKeyDown(event) {
            if (event.key === "Enter" && !event.shiftKey) {
                event.preventDefault();
                this.sendMessage();
            }
        },

        autoExpandTextarea(event) {
            const textarea = event.target;
            textarea.style.height = "auto";
            textarea.style.height = Math.min(textarea.scrollHeight, 120) + "px";
        },

        // Real-time
        handleIncomingMessage(message) {
            if (message.mode === this.mode) {
                const exists = this.messages.some((m) => m.id === message.id);
                if (!exists) {
                    this.messages.push(message);
                    this.$nextTick(() => this.scrollToBottom());
                }
            }

            if (this.isSuperadmin && this.isOpen) {
                this.loadUserList();
            }

            if (!this.isOpen) {
                this.showBrowserNotification(message);
                this.playNotificationSound();
            }

            this.updateUnreadCount();
        },

        // Mark as Read
        async markAsRead() {
            try {
                const unreadMessageIds = this.messages
                    .filter(
                        (m) =>
                            !m.is_read && m.receiver_id === this.currentUserId
                    )
                    .map((m) => m.id);

                if (unreadMessageIds.length === 0) {
                    this.updateUnreadCount();
                    return;
                }

                const url =
                    window.chatConfig?.routes?.markRead ||
                    "/admin/chat/mark-read";
                const response = await fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({ message_ids: unreadMessageIds }),
                });

                if (response.ok) {
                    this.messages.forEach((m) => {
                        if (unreadMessageIds.includes(m.id)) {
                            m.is_read = true;
                            m.read_at = new Date().toISOString();
                        }
                    });
                    this.updateUnreadCount();
                }
            } catch (error) {
                console.error("Failed to mark as read:", error);
            }
        },

        async updateUnreadCount() {
            try {
                const url =
                    window.chatConfig?.routes?.unreadCount ||
                    "/admin/chat/unread-count";
                const response = await fetch(url, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                });

                if (response.ok) {
                    const data = await response.json();
                    window.dispatchEvent(
                        new CustomEvent("chat-unread-updated", {
                            detail: { count: data.unread_count || 0 },
                        })
                    );
                }
            } catch (error) {
                console.error("Failed to update unread count:", error);
            }
        },

        // Notifications
        async requestNotificationPermission() {
            if (!("Notification" in window)) return;

            if (Notification.permission === "default") {
                try {
                    await Notification.requestPermission();
                } catch (error) {
                    console.error(
                        "Error requesting notification permission:",
                        error
                    );
                }
            }
        },

        showBrowserNotification(message) {
            if (!("Notification" in window)) return;
            if (Notification.permission !== "granted") return;
            if (this.isOpen) return;

            try {
                let senderName = "Pesan Baru";
                if (message.mode === "chatbot") {
                    senderName = "Chatbot";
                } else if (message.sender?.name) {
                    senderName = message.sender.name;
                } else if (this.isSuperadmin) {
                    senderName = "User";
                } else {
                    senderName = "Superadmin";
                }

                const notification = new Notification(senderName, {
                    body:
                        message.content.substring(0, 100) +
                        (message.content.length > 100 ? "..." : ""),
                    icon: "/images/chat-icon.png",
                    tag: "chat-message-" + message.id,
                    requireInteraction: false,
                });

                notification.onclick = () => {
                    window.focus();
                    this.openPanel();
                    notification.close();
                };

                setTimeout(() => notification.close(), 5000);
            } catch (error) {
                console.error("Error showing notification:", error);
            }
        },

        // Utilities
        scrollToBottom() {
            const container = this.$refs.messageList;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        playNotificationSound() {
            if (!this.soundEnabled) return;

            try {
                const audio = new Audio("/sounds/notification.mp3");
                audio.volume = 0.3;
                audio
                    .play()
                    .catch((err) => console.log("Could not play sound:", err));
            } catch (error) {
                console.log("Sound error:", error);
            }
        },

        toggleSound() {
            this.soundEnabled = !this.soundEnabled;
            localStorage.setItem("chat_sound_enabled", this.soundEnabled);

            window.dispatchEvent(
                new CustomEvent("chat-sound-toggled", {
                    detail: { enabled: this.soundEnabled },
                })
            );

            if (this.soundEnabled) {
                this.playNotificationSound();
            }
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            return date.toLocaleTimeString("id-ID", {
                hour: "2-digit",
                minute: "2-digit",
            });
        },

        formatDate(timestamp) {
            const date = new Date(timestamp);
            const today = new Date();
            const yesterday = new Date(today);
            yesterday.setDate(yesterday.getDate() - 1);

            if (date.toDateString() === today.toDateString()) {
                return "Hari ini";
            } else if (date.toDateString() === yesterday.toDateString()) {
                return "Kemarin";
            } else {
                return date.toLocaleDateString("id-ID", {
                    day: "numeric",
                    month: "short",
                });
            }
        },

        isSender(message) {
            return message.sender_id === this.currentUserId;
        },
    }));
});
