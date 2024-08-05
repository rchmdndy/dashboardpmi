<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_transaction_id
 * @property int $room_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Room $room
 * @property-read \App\Models\UserTransaction $user_transaction
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Booking whereUserTransactionId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperBooking {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $room_type_id
 * @property int $total_bookings
 * @property string $total_income
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \App\Models\RoomType $roomType
 * @method static \Illuminate\Database\Eloquent\Builder|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereRoomTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereTotalBookings($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereTotalIncome($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Report whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperReport {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $room_type_id
 * @property string $room_name
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $booking
 * @property-read int|null $booking_count
 * @property-read Room|null $parentRoom
 * @property-read \App\Models\RoomType $roomType
 * @method static \Illuminate\Database\Eloquent\Builder|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereRoomName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereRoomTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRoom {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $room_type_id
 * @property string $image_path
 * @property-read \App\Models\RoomType $roomType
 * @method static \Illuminate\Database\Eloquent\Builder|RoomImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoomImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoomImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoomImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomImage whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomImage whereRoomTypeId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRoomImage {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $room_type
 * @property int $capacity
 * @property string $price
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Report> $report
 * @property-read int|null $report_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Room> $room
 * @property-read int|null $room_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\RoomImage> $room_image
 * @property-read int|null $room_image_count
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereRoomType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRoomType {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $email
 * @property string $name
 * @property string $phone
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserTransaction> $user_transaction
 * @property-read int|null $user_transaction_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $user_email
 * @property string $order_id
 * @property int $room_type_id
 * @property string $start_date
 * @property string $end_date
 * @property string|null $snap_token
 * @property int $amount
 * @property string $total_price
 * @property string $transaction_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $booking
 * @property-read int|null $booking_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction whereRoomTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction whereSnapToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction whereTransactionStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTransaction whereUserEmail($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUserTransaction {}
}

