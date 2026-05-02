/**
 * Date Helper untuk format tanggal konsisten DD/MM/YYYY
 * Digunakan di seluruh aplikasi ERP
 */

window.DateHelper = {
    /**
     * Format tanggal ke DD/MM/YYYY
     */
    formatDate(date, separator = "/") {
        if (!date) return "-";

        try {
            const d = new Date(date);
            if (isNaN(d.getTime())) return "-";

            const day = String(d.getDate()).padStart(2, "0");
            const month = String(d.getMonth() + 1).padStart(2, "0");
            const year = d.getFullYear();

            return `${day}${separator}${month}${separator}${year}`;
        } catch (e) {
            console.warn("Error formatting date:", date, e);
            return "-";
        }
    },

    /**
     * Format tanggal dan waktu ke DD/MM/YYYY HH:mm
     */
    formatDateTime(date, separator = "/") {
        if (!date) return "-";

        try {
            const d = new Date(date);
            if (isNaN(d.getTime())) return "-";

            const day = String(d.getDate()).padStart(2, "0");
            const month = String(d.getMonth() + 1).padStart(2, "0");
            const year = d.getFullYear();
            const hours = String(d.getHours()).padStart(2, "0");
            const minutes = String(d.getMinutes()).padStart(2, "0");

            return `${day}${separator}${month}${separator}${year} ${hours}:${minutes}`;
        } catch (e) {
            console.warn("Error formatting datetime:", date, e);
            return "-";
        }
    },

    /**
     * Format tanggal untuk input HTML (YYYY-MM-DD)
     */
    formatForInput(date) {
        if (!date) return "";

        try {
            const d = new Date(date);
            if (isNaN(d.getTime())) return "";

            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, "0");
            const day = String(d.getDate()).padStart(2, "0");

            return `${year}-${month}-${day}`;
        } catch (e) {
            console.warn("Error formatting date for input:", date, e);
            return "";
        }
    },

    /**
     * Parse tanggal dari format DD/MM/YYYY
     */
    parseFromDDMMYYYY(dateString, separator = "/") {
        if (!dateString) return null;

        try {
            const parts = dateString.split(separator);
            if (parts.length !== 3) return null;

            const day = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10) - 1; // Month is 0-indexed
            const year = parseInt(parts[2], 10);

            const date = new Date(year, month, day);

            // Validate the date
            if (
                date.getDate() !== day ||
                date.getMonth() !== month ||
                date.getFullYear() !== year
            ) {
                return null;
            }

            return date;
        } catch (e) {
            console.warn("Error parsing date:", dateString, e);
            return null;
        }
    },

    /**
     * Get current date in DD/MM/YYYY format
     */
    today(separator = "/") {
        return this.formatDate(new Date(), separator);
    },

    /**
     * Get current datetime in DD/MM/YYYY HH:mm format
     */
    now(separator = "/") {
        return this.formatDateTime(new Date(), separator);
    },

    /**
     * Convert date to Indonesian format with day name
     */
    toIndonesian(date, includeTime = false) {
        if (!date) return "-";

        try {
            const d = new Date(date);
            if (isNaN(d.getTime())) return "-";

            const days = [
                "Minggu",
                "Senin",
                "Selasa",
                "Rabu",
                "Kamis",
                "Jumat",
                "Sabtu",
            ];
            const months = [
                "Januari",
                "Februari",
                "Maret",
                "April",
                "Mei",
                "Juni",
                "Juli",
                "Agustus",
                "September",
                "Oktober",
                "November",
                "Desember",
            ];

            const dayName = days[d.getDay()];
            const day = d.getDate();
            const monthName = months[d.getMonth()];
            const year = d.getFullYear();

            let result = `${dayName}, ${day} ${monthName} ${year}`;

            if (includeTime) {
                const hours = String(d.getHours()).padStart(2, "0");
                const minutes = String(d.getMinutes()).padStart(2, "0");
                result += ` ${hours}:${minutes}`;
            }

            return result;
        } catch (e) {
            console.warn("Error formatting Indonesian date:", date, e);
            return "-";
        }
    },

    /**
     * Validate date string in DD/MM/YYYY format
     */
    isValidDDMMYYYY(dateString, separator = "/") {
        const parsed = this.parseFromDDMMYYYY(dateString, separator);
        return parsed !== null;
    },

    /**
     * Get date range for current month
     */
    getCurrentMonthRange() {
        const now = new Date();
        const start = new Date(now.getFullYear(), now.getMonth(), 1);
        const end = new Date(now.getFullYear(), now.getMonth() + 1, 0);

        return {
            start: this.formatForInput(start),
            end: this.formatForInput(end),
            startFormatted: this.formatDate(start),
            endFormatted: this.formatDate(end),
        };
    },

    /**
     * Add days to a date
     */
    addDays(date, days) {
        const result = new Date(date);
        result.setDate(result.getDate() + days);
        return result;
    },

    /**
     * Get difference in days between two dates
     */
    diffInDays(date1, date2) {
        const d1 = new Date(date1);
        const d2 = new Date(date2);
        const diffTime = Math.abs(d2 - d1);
        return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    },
};

// Alias untuk kemudahan penggunaan
window.formatDate = window.DateHelper.formatDate.bind(window.DateHelper);
window.formatDateTime = window.DateHelper.formatDateTime.bind(
    window.DateHelper,
);

console.log("✅ DateHelper loaded successfully");
