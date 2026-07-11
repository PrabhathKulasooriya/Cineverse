<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReportIndexes extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index('payment_status');
            $table->index('created_at');
            $table->index('shows_show_id');
            $table->index('movies_movie_id');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('bookings_booking_id');
        });

        Schema::table('booked_seats', function (Blueprint $table) {
            $table->index('bookings_booking_id');
        });

        Schema::table('booking_snacks', function (Blueprint $table) {
            $table->index('booking_id');
            $table->index('snacks_idsnacks');
        });

        Schema::table('shows', function (Blueprint $table) {
            $table->index('date');
            $table->index('movies_movie_id');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['shows_show_id']);
            $table->dropIndex(['movies_movie_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['bookings_booking_id']);
        });

        Schema::table('booked_seats', function (Blueprint $table) {
            $table->dropIndex(['bookings_booking_id']);
        });

        Schema::table('booking_snacks', function (Blueprint $table) {
            $table->dropIndex(['booking_id']);
            $table->dropIndex(['snacks_idsnacks']);
        });

        Schema::table('shows', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['movies_movie_id']);
        });
    }
}