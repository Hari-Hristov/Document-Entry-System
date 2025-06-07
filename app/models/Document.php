namespace app\models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
protected $fillable = [
'name', 'file_path', 'category_id', 'status', 'qr_code', 'access_code', 'user_id'
];

public function category()
{
return $this->belongsTo(Category::class);
}

public function user()
{
return $this->belongsTo(User::class);
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
