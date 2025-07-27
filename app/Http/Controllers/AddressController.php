<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the address book
     */
    public function index()
    {
        $addresses = Address::where('user_id', Auth::id())
            ->orderBy('is_default_shipping', 'desc')
            ->orderBy('is_default_billing', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $shippingAddresses = $addresses->filter(function($address) {
            return in_array($address->address_type, ['shipping', 'both']);
        });

        $billingAddresses = $addresses->filter(function($address) {
            return in_array($address->address_type, ['billing', 'both']);
        });

        return view('addresses.index', compact('addresses', 'shippingAddresses', 'billingAddresses'));
    }

    /**
     * Show the form for creating a new address
     */
    public function create()
    {
        $addressTypes = Address::getAddressTypes();
        $usageTypes = Address::getUsageTypes();
        
        return view('addresses.create', compact('addressTypes', 'usageTypes'));
    }

    /**
     * Store a newly created address
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => ['required', Rule::in(['home', 'work', 'other'])],
            'label' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'address_type' => ['required', Rule::in(['shipping', 'billing', 'both'])],
            'delivery_instructions' => 'nullable|string|max:500',
            'is_default_shipping' => 'boolean',
            'is_default_billing' => 'boolean',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();

        $address = Address::create($data);

        // Handle default address settings
        if ($request->is_default_shipping) {
            $address->setAsDefaultShipping();
        }

        if ($request->is_default_billing) {
            $address->setAsDefaultBilling();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Address added successfully!',
                'address' => $address->load('user')
            ]);
        }

        return redirect()->route('addresses.index')->with('success', 'Address added successfully!');
    }

    /**
     * Display the specified address
     */
    public function show(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        return view('addresses.show', compact('address'));
    }

    /**
     * Show the form for editing the specified address
     */
    public function edit(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        // If it's an AJAX request, return JSON data for the modal
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'address' => $address
            ]);
        }

        // Otherwise return the edit view (if needed)
        $addressTypes = Address::getAddressTypes();
        $usageTypes = Address::getUsageTypes();

        return view('addresses.edit', compact('address', 'addressTypes', 'usageTypes'));
    }

    /**
     * Update the specified address
     */
    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'type' => ['required', Rule::in(['home', 'work', 'other'])],
            'label' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'landmark' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'address_type' => ['required', Rule::in(['shipping', 'billing', 'both'])],
            'delivery_instructions' => 'nullable|string|max:500',
            'is_default_shipping' => 'boolean',
            'is_default_billing' => 'boolean',
        ]);

        $address->update($request->all());

        // Handle default address settings
        if ($request->is_default_shipping) {
            $address->setAsDefaultShipping();
        }

        if ($request->is_default_billing) {
            $address->setAsDefaultBilling();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully!',
                'address' => $address->fresh()
            ]);
        }

        return redirect()->route('addresses.index')->with('success', 'Address updated successfully!');
    }

    /**
     * Remove the specified address
     */
    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully!'
            ]);
        }

        return redirect()->route('addresses.index')->with('success', 'Address deleted successfully!');
    }

    /**
     * Set address as default shipping
     */
    public function setDefaultShipping(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->setAsDefaultShipping();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Default shipping address updated!'
            ]);
        }

        return back()->with('success', 'Default shipping address updated!');
    }

    /**
     * Set address as default billing
     */
    public function setDefaultBilling(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->setAsDefaultBilling();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Default billing address updated!'
            ]);
        }

        return back()->with('success', 'Default billing address updated!');
    }

    /**
     * Get addresses for checkout (AJAX)
     */
    public function getForCheckout(Request $request)
    {
        $type = $request->get('type', 'shipping'); // shipping or billing
        
        $addresses = Address::where('user_id', Auth::id());
        
        if ($type === 'shipping') {
            $addresses = $addresses->forShipping();
        } elseif ($type === 'billing') {
            $addresses = $addresses->forBilling();
        }
        
        $addresses = $addresses->orderBy('is_default_' . $type, 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'addresses' => $addresses
        ]);
    }

    /**
     * Validate address (for real-time validation)
     */
    public function validateAddress(Request $request)
    {
        $request->validate([
            'postal_code' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
        ]);

        // Here you could integrate with address validation APIs
        // For now, we'll do basic validation
        
        $isValid = true;
        $suggestions = [];
        
        // Basic postal code validation for India
        if ($request->country === 'India' && !preg_match('/^[1-9][0-9]{5}$/', $request->postal_code)) {
            $isValid = false;
            $suggestions[] = 'Please enter a valid 6-digit postal code';
        }

        return response()->json([
            'valid' => $isValid,
            'suggestions' => $suggestions
        ]);
    }
}