/**
 * User Avatar Helper Functions
 * Dùng chung cho create/edit/show pages
 */

// Kiểm tra file ảnh hợp lệ
function isValidImageFile(file) {
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
    const maxSize = 2 * 1024 * 1024; // 2MB

    if (!validTypes.includes(file.type)) {
        return {
            valid: false,
            message: 'Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)!'
        };
    }

    if (file.size > maxSize) {
        return {
            valid: false,
            message: 'Kích thước file vượt quá 2MB!'
        };
    }

    return { valid: true };
}

// Preview avatar với validation
function previewAvatarWithValidation(file, previewElementId, removeButtonId = null) {
    const validation = isValidImageFile(file);
    
    if (!validation.valid) {
        if (typeof toastr !== 'undefined') {
            toastr.error(validation.message);
        } else {
            alert(validation.message);
        }
        return false;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById(previewElementId);
        
        // Nếu preview là div (initials), chuyển thành img
        if (preview.tagName === 'DIV') {
            const img = document.createElement('img');
            img.id = previewElementId;
            img.className = preview.className.replace('d-flex align-items-center justify-content-center', '');
            img.style.cssText = preview.style.cssText;
            img.src = e.target.result;
            preview.replaceWith(img);
        } else {
            preview.src = e.target.result;
        }

        // Show remove button nếu có
        if (removeButtonId) {
            const removeBtn = document.getElementById(removeButtonId);
            if (removeBtn) {
                removeBtn.classList.remove('d-none');
            }
        }
    };
    
    reader.readAsDataURL(file);
    return true;
}

// Xóa preview avatar
function clearAvatarPreview(previewElementId, fileInputId, removeButtonId, defaultSrc = null, userInitials = null) {
    const preview = document.getElementById(previewElementId);
    const fileInput = document.getElementById(fileInputId);
    const removeBtn = document.getElementById(removeButtonId);

    // Reset file input
    if (fileInput) {
        fileInput.value = '';
    }

    // Hide remove button
    if (removeBtn) {
        removeBtn.classList.add('d-none');
    }

    // Reset preview
    if (defaultSrc) {
        // Có ảnh mặc định -> hiển thị img
        if (preview.tagName === 'DIV') {
            const img = document.createElement('img');
            img.id = previewElementId;
            img.className = 'rounded-circle border border-4 border-primary shadow-lg';
            img.style.cssText = 'width: 200px; height: 200px; object-fit: cover;';
            img.src = defaultSrc;
            preview.replaceWith(img);
        } else {
            preview.src = defaultSrc;
        }
    } else if (userInitials) {
        // Không có ảnh -> hiển thị initials
        if (preview.tagName === 'IMG') {
            const div = document.createElement('div');
            div.id = previewElementId;
            div.className = 'rounded-circle bg-gradient-primary text-white d-flex align-items-center justify-content-center shadow-lg mx-auto';
            div.style.cssText = 'width: 200px; height: 200px; font-size: 4rem; font-weight: bold;';
            div.textContent = userInitials;
            preview.replaceWith(div);
        }
    }
}

// Compress ảnh trước khi upload (tùy chọn)
function compressImage(file, maxWidth = 800, quality = 0.8) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                if (width > maxWidth) {
                    height = (height * maxWidth) / width;
                    width = maxWidth;
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob(
                    (blob) => {
                        resolve(new File([blob], file.name, {
                            type: file.type,
                            lastModified: Date.now()
                        }));
                    },
                    file.type,
                    quality
                );
            };
            img.onerror = reject;
            img.src = e.target.result;
        };
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

// Generate initials từ tên
function generateInitials(firstName, lastName, username = null) {
    if (firstName && lastName) {
        return (firstName.charAt(0) + lastName.charAt(0)).toUpperCase();
    }
    if (username) {
        return username.substring(0, 2).toUpperCase();
    }
    return 'U';
}

// Export functions để sử dụng trong blade templates
if (typeof window !== 'undefined') {
    window.AvatarHelper = {
        isValidImageFile,
        previewAvatarWithValidation,
        clearAvatarPreview,
        compressImage,
        generateInitials
    };
}