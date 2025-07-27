<div class="profile-form-section">
    <div class="alert alert-danger" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>{{ __('Warning:') }}</strong> {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </div>

    <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
        <i class="fas fa-trash-alt me-2"></i>{{ __('Delete Account') }}
    </button>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>{{ __('Confirm Account Deletion') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form method="post" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                        </div>
                        
                        <h6 class="text-center mb-3">{{ __('Are you sure you want to delete your account?') }}</h6>
                        
                        <p class="text-muted text-center mb-4">
                            {{ __('This action cannot be undone. All of your data, orders, and account information will be permanently deleted.') }}
                        </p>
                        
                        <div class="mb-3">
                            <label for="delete_password" class="form-label">
                                <i class="fas fa-lock me-2"></i>{{ __('Enter your password to confirm') }}
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                                       id="delete_password" name="password" 
                                       placeholder="{{ __('Your current password') }}" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleDeletePassword()">
                                    <i class="fas fa-eye" id="delete-password-icon"></i>
                                </button>
                            </div>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="confirmDeletion" required>
                            <label class="form-check-label text-danger" for="confirmDeletion">
                                <strong>{{ __('I understand that this action is permanent and cannot be undone.') }}</strong>
                            </label>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                            <i class="fas fa-trash-alt me-2"></i>{{ __('Delete My Account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function toggleDeletePassword() {
        const field = document.getElementById('delete_password');
        const icon = document.getElementById('delete-password-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Enable delete button only when checkbox is checked and password is entered
    document.addEventListener('DOMContentLoaded', function() {
        const confirmCheckbox = document.getElementById('confirmDeletion');
        const passwordField = document.getElementById('delete_password');
        const deleteBtn = document.getElementById('confirmDeleteBtn');
        
        function toggleDeleteButton() {
            deleteBtn.disabled = !(confirmCheckbox.checked && passwordField.value.length > 0);
        }
        
        confirmCheckbox.addEventListener('change', toggleDeleteButton);
        passwordField.addEventListener('input', toggleDeleteButton);
        
        // Show modal if there are validation errors
        @if($errors->userDeletion->isNotEmpty())
            var deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            deleteModal.show();
        @endif
    });
    </script>
</div>
