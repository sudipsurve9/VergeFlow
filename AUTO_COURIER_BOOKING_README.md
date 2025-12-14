# Automatic Courier Booking System

## Overview
This system automatically books courier services (like Shiprocket) when a customer places an order, based on delivery pincode availability. No manual intervention is required.

## How It Works

### 1. **Order Creation Flow**
When a customer places an order:
1. Order is created in the database
2. Order items are saved
3. Cart is cleared
4. **Automatic courier booking is triggered** (NEW!)

### 2. **Automatic Booking Process**

```
Order Created
    ↓
Extract Delivery Pincode
    ↓
Check Courier Availability (Shiprocket API)
    ↓
If Available → Book Shipment Automatically
    ↓
Update Order with:
    - Tracking Number (AWB)
    - Courier Name
    - Status → "processing"
    - Delivery Status → "in_transit"
```

### 3. **What Happens on Success**
- ✅ Order status changes to `processing`
- ✅ Tracking number (AWB) is saved
- ✅ Courier name is saved
- ✅ Status history is updated
- ✅ Customer can track their order immediately

### 4. **What Happens on Failure**
- ⚠️ Order is still created successfully
- ⚠️ Order status remains `pending`
- ⚠️ Status history notes the failure
- ⚠️ Admin can manually book courier later
- ⚠️ No customer impact - order is still valid

## Files Created/Modified

### New Files:
1. **`app/Services/AutoCourierBookingService.php`**
   - Main service for automatic courier booking
   - Handles pincode extraction
   - Checks courier availability
   - Books shipment automatically

### Modified Files:
1. **`app/Http/Controllers/OrderController.php`**
   - Added auto-booking call after order creation
   - Works in both `processCheckout()` and `store()` methods

2. **`app/Services/ShiprocketService.php`**
   - Enhanced `createOrder()` method
   - Added `extractAddressFromOrder()` helper
   - Better address handling from Order model

## Configuration

### Required Setup:
1. **Shiprocket Integration** must be configured in Admin Panel:
   - Go to: Admin → API Integrations
   - Add Shiprocket integration with:
     - Email
     - Password
     - Login URL (default: `https://apiv2.shiprocket.in/v1/external/auth/login`)

2. **Pickup Pincode** (Optional - defaults to 110001):
   - Set in `.env`: `SHIPROCKET_PICKUP_PINCODE=110001`
   - Or in `config/services.php`

### Address Requirements:
- Orders must have a valid shipping address with pincode
- Pincode can be extracted from:
  - Address model relationship (`shippingAddress->postal_code`)
  - Address string (6-digit Indian pincode format)

## Features

### ✅ Automatic Features:
- ✅ Pincode extraction from address
- ✅ Courier availability check
- ✅ Automatic shipment booking
- ✅ Tracking number assignment
- ✅ Order status updates
- ✅ Status history logging

### ✅ Error Handling:
- ✅ Graceful failure (order still created)
- ✅ Detailed error logging
- ✅ Manual review flagging
- ✅ No customer impact on failure

### ✅ Smart Logic:
- ✅ Checks courier availability before booking
- ✅ Handles multiple address formats
- ✅ Works with both Address model and string addresses
- ✅ Automatic retry on token expiration (via ShiprocketService)

## Usage

### For Customers:
- **No changes needed!** Orders are automatically booked
- Tracking number appears immediately if booking succeeds
- Order is still valid even if auto-booking fails

### For Admins:
- View orders in Admin Panel
- Orders with failed auto-booking will show status: `pending`
- Check status history for booking details
- Manually book courier if needed (existing functionality)

## Testing

### Test Scenarios:
1. **Successful Booking:**
   - Place order with valid pincode
   - Check order status → should be `processing`
   - Check tracking number → should have AWB code

2. **Failed Booking (No Courier):**
   - Place order with unsupported pincode
   - Order should still be created
   - Status should remain `pending`
   - Check logs for details

3. **Failed Booking (No Integration):**
   - Remove Shiprocket integration
   - Place order
   - Order should still be created
   - Status history should note the issue

## Logs

All booking attempts are logged:
- **Location:** `storage/logs/laravel.log`
- **Search for:** "Auto courier booking"
- **Includes:** Order ID, AWB code, errors, etc.

## Future Enhancements

Possible improvements:
- [ ] Support multiple courier vendors (Delhivery, BlueDart, etc.)
- [ ] Smart courier selection based on cost/speed
- [ ] Retry mechanism for failed bookings
- [ ] Email notifications on booking status
- [ ] Admin dashboard for booking statistics
- [ ] Queue-based booking for better performance

## Troubleshooting

### Issue: Auto-booking not working
**Solution:**
1. Check Shiprocket integration is configured
2. Check logs: `storage/logs/laravel.log`
3. Verify pincode is in address
4. Check API credentials are correct

### Issue: Orders created but no tracking number
**Solution:**
1. Check order status history
2. Review logs for error messages
3. Manually book courier from admin panel
4. Verify Shiprocket API is accessible

### Issue: Wrong pincode extracted
**Solution:**
1. Ensure addresses use Address model (not string)
2. Verify `postal_code` field is populated
3. Check address format in database

---

**Note:** This system is designed to be non-blocking. Even if automatic booking fails, the order is still created and can be processed manually.

