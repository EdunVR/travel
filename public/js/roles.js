/**
 * Role Management JavaScript
 * Handles role and permission management functionality
 */

// Ensure the function is available immediately
window.roleManagement = function() {
    return {
        roles: window.rolesData || [],
        permissions: window.permissionsData || [],
        
        init() {
            console.log('✅ Role Management initialized');
            console.log('📊 Roles loaded:', this.roles.length);
            console.log('🔐 Permissions loaded:', this.permissions.length);
        },
        
        isProtectedRole(roleName) {
            const protectedRoles = ['super_admin', 'admin', 'user'];
            const normalized = roleName.toLowerCase().replace(/ /g, '_');
            return protectedRoles.includes(normalized);
        },
        
        groupPermissions(permissions) {
            if (!permissions) return {};
            return permissions.reduce((groups, perm) => {
                const group = perm.module || 'Other';
                if (!groups[group]) groups[group] = [];
                groups[group].push(perm);
                return groups;
            }, {});
        },
        
        openCreateModal() {
            // Reset form
            $('#roleForm')[0].reset();
            $('#roleId').val('');
            $('#roleName').val('');
            $('#roleDisplayName').val('');
            $('#roleDescription').val('');
            $('#modalTitle').text('Tambah Role');
            $('.permission-checkbox').prop('checked', false);
            $('.select-group').prop('checked', false);
            
            $('#roleModal').modal('show');
        },
        
        editRole(role) {
            $('#roleModal').modal('show');
            $('#roleId').val(role.id);
            $('#roleName').val(role.name);
            $('#roleDisplayName').val(role.display_name);
            $('#roleDescription').val(role.description);
            $('#modalTitle').text('Edit Role');
            
            $('.permission-checkbox').prop('checked', false);
            if (role.permissions) {
                role.permissions.forEach(perm => {
                    $(`#perm_${perm.id}`).prop('checked', true);
                });
            }
        },
        
        deleteRole(role) {
            if (confirm(`Apakah Anda yakin ingin menghapus role "${role.display_name}"?`)) {
                $.ajax({
                    url: `${window.baseUrl}/admin/roles/${role.id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: (response) => {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        }
                    },
                    error: (xhr) => {
                        alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
                    }
                });
            }
        }
    }
};

// jQuery handlers for modal functionality
$(document).ready(function() {
    console.log('🔧 Role management jQuery handlers initialized');
    
    // Select all permissions in a module
    $(document).on('click', '.select-module', function() {
        const module = $(this).data('module');
        const checked = $(this).prop('checked');
        $(`.permission-checkbox[data-module="${module}"]`).prop('checked', checked);
        $(`.select-menu[data-module="${module}"]`).prop('checked', checked);
    });

    // Select all permissions in a menu
    $(document).on('click', '.select-menu', function() {
        const module = $(this).data('module');
        const menu = $(this).data('menu');
        const checked = $(this).prop('checked');
        $(`.permission-checkbox[data-module="${module}"][data-menu="${menu}"]`).prop('checked', checked);
    });

    // Form submit handler
    $('#roleForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get all checked permissions and remove duplicates
        const checkedPermissions = [];
        $('.permission-checkbox:checked').each(function() {
            const permId = $(this).val();
            if (permId && !checkedPermissions.includes(permId)) {
                checkedPermissions.push(permId);
            }
        });
        
        // Log for debugging
        console.log('📤 Submitting permissions:', checkedPermissions);
        
        // Create form data manually to ensure no duplicates
        const formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('name', $('#roleName').val());
        formData.append('display_name', $('#roleDisplayName').val());
        formData.append('description', $('#roleDescription').val());
        
        // Add unique permissions
        checkedPermissions.forEach(permId => {
            formData.append('permissions[]', permId);
        });
        
        const roleId = $('#roleId').val();
        const url = roleId ? `${window.baseUrl}/admin/roles/${roleId}` : `${window.baseUrl}/admin/roles`;
        const method = roleId ? 'PUT' : 'POST';
        
        // Add method override for PUT
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }
        
        $.ajax({
            url: url,
            type: 'POST', // Always POST, but with _method override for PUT
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                console.error('❌ Error response:', xhr.responseJSON);
                const errors = xhr.responseJSON?.errors;
                if (errors) {
                    let errorMsg = '';
                    Object.keys(errors).forEach(key => {
                        errorMsg += errors[key][0] + '\n';
                    });
                    alert(errorMsg);
                } else {
                    alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
                }
            }
        });
    });
});

console.log('✅ roles.js loaded successfully');