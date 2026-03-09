<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Hotel fields (booking)
            $table->string('hotel_booking_number')->nullable()->after('hotel_extra_info');
            $table->decimal('hotel_fnb_credit', 10, 2)->nullable()->after('hotel_booking_number');
            $table->boolean('hotel_fnb_credit_approved')->default(false)->after('hotel_fnb_credit');

            // Ground transport fields (booking)
            $table->string('gt_driver_name')->nullable()->after('hotel_fnb_credit_approved');
            $table->string('gt_driver_phone')->nullable()->after('gt_driver_name');
            $table->string('gt_car_type')->nullable()->after('gt_driver_phone');
            $table->string('gt_airport_hotel')->nullable()->after('gt_car_type');
            $table->string('gt_hotel_venue')->nullable()->after('gt_airport_hotel');
            $table->string('gt_venue_hotel')->nullable()->after('gt_hotel_venue');
            $table->string('gt_hotel_airport')->nullable()->after('gt_venue_hotel');

            // Booking fee fields
            $table->decimal('booking_fee', 10, 2)->nullable()->after('status');
            $table->string('booking_fee_note')->nullable()->after('booking_fee');

            // Invoicing fields (booking)
            $table->string('invoice_company_name')->nullable()->after('booking_fee_note');
            $table->string('invoice_vat_number')->nullable()->after('invoice_company_name');
            $table->string('invoice_coc_number')->nullable()->after('invoice_vat_number');
            $table->string('invoice_contact_person')->nullable()->after('invoice_coc_number');
            $table->date('invoice_payment_date')->nullable()->after('invoice_contact_person');
            $table->boolean('invoice_sent')->default(false)->after('invoice_payment_date');

            // Travel booking number
            $table->string('travel_booking_number')->nullable()->after('flight_number');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'hotel_booking_number',
                'hotel_fnb_credit',
                'hotel_fnb_credit_approved',
                'gt_driver_name',
                'gt_driver_phone',
                'gt_car_type',
                'gt_airport_hotel',
                'gt_hotel_venue',
                'gt_venue_hotel',
                'gt_hotel_airport',
                'booking_fee',
                'booking_fee_note',
                'invoice_company_name',
                'invoice_vat_number',
                'invoice_coc_number',
                'invoice_contact_person',
                'invoice_payment_date',
                'invoice_sent',
                'travel_booking_number',
            ]);
        });
    }
};
