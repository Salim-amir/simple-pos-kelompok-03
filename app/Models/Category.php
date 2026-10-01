<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Category extends Model
{
public function products(): HasMany
{
return $this->hasMany(Product::class);
}
}
Ganti seluruh isi app/Models/Product.php:
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Product extends Model
{
public function category(): BelongsTo
{
return $this->belongsTo(Category::class);
}
}
