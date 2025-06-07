namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
protected $fillable = [
'document_id', 'user_id', 'action', 'duration', 'created_at'
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
