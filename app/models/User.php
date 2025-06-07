namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
protected $fillable = [
'name', 'email', 'password'
];

public function documents()
{
return $this->hasMany(Document::class);
}

public function logs()
{
return $this->hasMany(Log::class);
}

public function cryptoKeys()
{
return $this->hasMany(CryptoKey::class);
}
}
