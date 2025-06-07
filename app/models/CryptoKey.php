namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoKey extends Model
{
protected $fillable = [
'document_id', 'user_id', 'key_fragment'
];

public function document()
{
return $this->belongsTo(Document::class);
}

public function user()
{
return $this->belongsTo(User::class);
}
}
