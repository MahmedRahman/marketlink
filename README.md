# MarketLink

نظام إدارة بيانات العملاء - Dashboard لتسجيل وإدارة بيانات العملاء

## المميزات

- ✅ صفحة تسجيل دخول للإدمن كصفحة رئيسية
- ✅ واجهة مستخدم عربية (RTL)
- ✅ تصميم عصري وجذاب باستخدام CSS
- ✅ نظام مصادقة كامل
- ✅ لوحة تحكم Dashboard

## المتطلبات

- PHP >= 8.2
- Composer
- SQLite (أو أي قاعدة بيانات أخرى)

## التثبيت

1. استنساخ المشروع:
```bash
cd marketlink
```

2. تثبيت المكتبات:
```bash
composer install
```

3. إعداد ملف البيئة:
```bash
cp .env.example .env
php artisan key:generate
```

4. إعداد قاعدة البيانات:
```bash
php artisan migrate
```

5. إنشاء مستخدم إدمن (اختياري):
```bash
php artisan tinker
```
ثم في Tinker:
```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@marketlink.com',
    'password' => Hash::make('password')
]);
```

6. تشغيل السيرفر:
```bash
php artisan serve
```

## البنية

- `resources/views/auth/login.blade.php` - صفحة تسجيل الدخول
- `resources/views/dashboard.blade.php` - لوحة التحكم
- `public/css/auth.css` - تنسيقات صفحة تسجيل الدخول
- `public/css/dashboard.css` - تنسيقات لوحة التحكم
- `app/Http/Controllers/Auth/LoginController.php` - Controller لتسجيل الدخول

## الاستخدام

1. افتح المتصفح على: `http://localhost:8000`
2. سترى صفحة تسجيل الدخول مباشرة
3. أدخل بيانات المستخدم المسجل
4. بعد تسجيل الدخول، ستنتقل إلى لوحة التحكم

## التطوير المستقبلي

- إضافة نظام إدارة العملاء (CRUD)
- إضافة تقارير وإحصائيات
- إضافة صلاحيات متعددة للمستخدمين

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
