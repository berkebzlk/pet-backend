# DataGrid Helper Kullanım Kılavuzu

## Genel Bakış

`DataGridHelper` sınıfı, Laravel uygulamalarında datagrid özelliklerini (pagination, sorting, search, filtering) kolayca uygulamanızı sağlar. Tek bir `index` metodu ile tüm özellikler kullanılabilir.

## Özellikler

- **Pagination**: Sayfalama desteği
- **Sorting**: Çoklu sütun sıralama
- **Search**: Çoklu alan arama
- **Filtering**: Gelişmiş filtreleme
- **Field Selection**: Dinamik alan seçimi

## Temel Kullanım

### 1. Service'de Kullanım

```php
// BaseService'de otomatik olarak mevcut
public function index(array $requestData = [])
{
    // Her zaman LengthAwarePaginator döndürür
    // Default pagination: 15 items per page
    // Request data boşsa: default pagination ile tüm veriler
    // Request data varsa: DataGrid özelliklerini uygular
}
```

### 2. Controller'da Kullanım

```php
public function index(Request $request)
{
    $results = $this->service->index($request->all());

    // Her zaman pagination bilgileri ile döndür
    return ResponseHelper::success([
        'data' => Resource::collection($results->items()),
        'pagination' => [
            'current_page' => $results->currentPage(),
            'per_page' => $results->perPage(),
            'total' => $results->total(),
            'last_page' => $results->lastPage(),
            'from' => $results->firstItem(),
            'to' => $results->lastItem()
        ]
    ]);
}
```

## API Kullanım Örnekleri

### 1. Tüm Veriler (Default Pagination)

```http
GET /api/roles
```

**Response:**
```json
{
  "success": true,
  "data": [
    {"id": 1, "name": "Admin", "created_at": "2023-01-01"},
    {"id": 2, "name": "User", "created_at": "2023-01-02"}
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 2,
    "last_page": 1,
    "from": 1,
    "to": 2
  }
}
```

### 2. Pagination

```http
GET /api/roles?per_page=10&page=2
```

**Response:**
```json
{
  "success": true,
  "data": [...],
  "pagination": {
    "current_page": 2,
    "per_page": 10,
    "total": 50,
    "last_page": 5,
    "from": 11,
    "to": 20
  }
}
```

### 3. Arama

```http
GET /api/roles?search=admin
```

### 4. Sıralama

```http
GET /api/roles?sort_by={"name":"asc","created_at":"desc"}
```

### 5. Filtreleme

```http
GET /api/roles?filters={"status":"active","created_at":{"min":"2023-01-01","max":"2023-12-31"}}
```

### 6. Alan Seçimi

```http
GET /api/roles?fields=["id","name","created_at"]
```

### 7. Kombine Kullanım

```http
GET /api/roles?per_page=20&page=1&search=admin&sort_by={"name":"asc"}&filters={"status":"active"}&fields=["id","name","email"]
```

## Gelişmiş Kullanım

### Model'de Özel Ayarlar

```php
class User extends Model
{
    // Arama yapılabilir alanlar
    public function getSearchableFields(): array
    {
        return ['name', 'email', 'phone'];
    }
    
    // Sıralanabilir alanlar
    public function getSortableFields(): array
    {
        return ['name', 'email', 'created_at', 'updated_at'];
    }
}
```

### Manuel Kullanım

```php
use App\Modules\Core\Helpers\DataGridHelper;

$query = User::query();
$dataGrid = new DataGridHelper($query);

$dataGrid->setSearchableFields(['name', 'email'])
         ->setSortableFields(['name', 'created_at'])
         ->setSelectFields(['id', 'name', 'email'])
         ->setPagination(15, 1)
         ->setSearchTerm('john')
         ->setSorting(['name' => 'asc'])
         ->setFilters(['status' => 'active']);

$results = $dataGrid->getResults();
```

## Request'ten Otomatik Kullanım

```php
use App\Modules\Core\Helpers\DataGridHelper;

$query = User::query();
$dataGrid = DataGridHelper::fromRequest($query, $request);
$results = $dataGrid->getResults();
```

## Filtreleme Türleri

### 1. Basit Eşitlik

```json
{
  "status": "active"
}
```

### 2. LIKE Arama

```json
{
  "name": "john"
}
```

### 3. Aralık Filtreleme

```json
{
  "created_at": {
    "min": "2023-01-01",
    "max": "2023-12-31"
  }
}
```

### 4. Sayısal Aralık

```json
{
  "age": {
    "min": 18,
    "max": 65
  }
}
```

## Response Format

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7,
    "from": 1,
    "to": 15
  }
}
```

## Performans İpuçları

1. **Index Kullanımı**: Sık kullanılan arama alanları için database index'leri oluşturun
2. **Field Selection**: Gereksiz alanları seçmeyin
3. **Pagination**: Büyük veri setleri için pagination kullanın
4. **Caching**: Sık kullanılan sorgular için cache uygulayın

## Otomatik Validasyon

Service otomatik olarak şu validasyonları yapar:

### 1. Pagination Validasyonu
```php
// Geçersiz değerler default değerlere dönüştürülür
$perPage = is_numeric($perPage) && $perPage > 0 ? (int)$perPage : 15;
$page = is_numeric($page) && $page > 0 ? (int)$page : 1;
```

### 2. String Validasyonu
```php
// Search term temizlenir
$searchTerm = is_string($searchTerm) ? trim($searchTerm) : '';
```

### 3. JSON Decode
```php
// JSON string'ler otomatik decode edilir
if (is_string($sortBy)) {
    $sortBy = json_decode($sortBy, true) ?? [];
}
```

### 4. Array Validasyonu
```php
// Geçersiz array'ler boş array'e dönüştürülür
$sortBy = is_array($sortBy) ? $sortBy : [];
$filters = is_array($filters) ? $filters : [];
$selectFields = is_array($selectFields) ? $selectFields : [];
```

## Hata Yönetimi

```php
try {
    $results = $this->service->index($request->all());
} catch (\Exception $e) {
    return ResponseHelper::error('Veri yüklenirken hata oluştu: ' . $e->getMessage());
}
```
