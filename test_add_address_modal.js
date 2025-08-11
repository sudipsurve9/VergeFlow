// Test script to simulate Add Address modal interaction
console.log('🧪 Testing Add Address Modal Functionality');

// Function to test modal opening
function testAddAddressModal() {
    console.log('📋 Testing Add Address Modal...');
    
    // Check if modal exists
    const modal = document.getElementById('addressModal');
    if (!modal) {
        console.error('❌ Address modal not found in DOM');
        return false;
    }
    console.log('✅ Address modal found in DOM');
    
    // Check if Bootstrap modal is available
    if (typeof bootstrap === 'undefined') {
        console.error('❌ Bootstrap not loaded');
        return false;
    }
    console.log('✅ Bootstrap is available');
    
    // Test opening the modal
    try {
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
        console.log('✅ Modal opened successfully');
        
        // Check if form fields are present
        const requiredFields = ['name', 'phone', 'address_line1', 'city', 'state', 'postal_code'];
        let allFieldsPresent = true;
        
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (!field) {
                console.error(`❌ Required field missing: ${fieldId}`);
                allFieldsPresent = false;
            } else {
                console.log(`✅ Field found: ${fieldId}`);
            }
        });
        
        if (allFieldsPresent) {
            console.log('✅ All required form fields are present');
        }
        
        // Test form submission button
        const saveBtn = document.getElementById('saveAddressBtn');
        if (saveBtn) {
            console.log('✅ Save Address button found');
        } else {
            console.error('❌ Save Address button not found');
        }
        
        // Close modal after test
        setTimeout(() => {
            modalInstance.hide();
            console.log('✅ Modal closed after test');
        }, 2000);
        
        return allFieldsPresent;
        
    } catch (error) {
        console.error('❌ Error opening modal:', error);
        return false;
    }
}

// Function to test form validation
function testFormValidation() {
    console.log('🔍 Testing form validation...');
    
    const form = document.getElementById('addressForm');
    if (!form) {
        console.error('❌ Address form not found');
        return false;
    }
    
    // Test with empty form
    const formData = new FormData(form);
    console.log('📋 Form data entries:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
    }
    
    return true;
}

// Run tests when page is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            testAddAddressModal();
            testFormValidation();
        }, 1000);
    });
} else {
    setTimeout(() => {
        testAddAddressModal();
        testFormValidation();
    }, 1000);
}
