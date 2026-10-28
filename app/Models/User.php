<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'alamat',
        'email',
        'image',
        'password',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function totalKeranjang(): int
    {
        return Temp::where('id_user', $this->id)->count();
    }

    public function totalBooking(): int
    {
        return BookingDetail::whereIn('id_booking', function ($query) {
            $query->select('id_booking')
                ->from('booking')
                ->where('id_user', $this->id);
        })->count();
    }

    public function totalSedangPinjam(): int
    {
        return PinjamDetail::where('status', 'Pinjam')
            ->whereIn('no_pinjam', function ($query) {
                $query->select('no_pinjam')
                    ->from('pinjam')
                    ->where('id_user', $this->id);
            })
            ->count();
    }

    public function totalRiwayatPinjam(): int
    {
        return PinjamDetail::whereIn('no_pinjam', function ($query) {
            $query->select('no_pinjam')
                ->from('pinjam')
                ->where('id_user', $this->id);
        })->count();
    }
}
