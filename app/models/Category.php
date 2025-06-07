namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
protected $fillable = [
'name', 'responsible_user_id'
];

public function documents()
{
return $this->hasMany(Document::class);
}

public function responsible()
{
return $this->belongsTo(User::class, 'responsible_user_id');
}
}
