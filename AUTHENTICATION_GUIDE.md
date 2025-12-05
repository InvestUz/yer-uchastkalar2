# Yer.toshkentinvest.uz - Authentication & Authorization System

## 🔐 System Overview

This system implements **role-based access control** with two types of users:
1. **Super Admin** - Full access with edit permissions
2. **District Users** - View-only access, filtered by district

---

## 👥 User Accounts

### Super Admin
- **Email**: `admin@toshkentinvest.uz`
- **Password**: `Admin@2025`
- **Role**: Super Administrator (Дав актив Тошкент ш бошқарма)
- **Permissions**:
  - Full access to all districts
  - Can edit, create, and delete records
  - Can manage global settings (qoldiq)
  - Can access all pages

### District Users (Худуд фойдаланувчилари)
All district users have the password: `District@2025`

| № | District (Ҳудуд) | Email |
|---|-----------------|-------|
| 1 | Бектемир тумани | bektemir@toshkentinvest.uz |
| 2 | Мирзо Улуғбек тумани | mirzo_ulugbek@toshkentinvest.uz |
| 3 | Миробод тумани | mirobod@toshkentinvest.uz |
| 4 | Олмазор тумани | olmazor@toshkentinvest.uz |
| 5 | Сирғали тумани | sirgali@toshkentinvest.uz |
| 6 | Учтепа тумани | uchtepa@toshkentinvest.uz |
| 7 | Чилонзор тумани | chilonzor@toshkentinvest.uz |
| 8 | Шайхонтоҳур тумани | shayxontohur@toshkentinvest.uz |
| 9 | Юнусобод тумани | yunusobod@toshkentinvest.uz |
| 10 | Яккасарой тумани | yakkasaroy@toshkentinvest.uz |
| 11 | Янги ҳаёт тумани | yangi_hayot@toshkentinvest.uz |
| 12 | Яшнобод тумани | yashnobod@toshkentinvest.uz |

**Permissions**:
- View-only access
- See only their district's data
- Cannot edit or create records
- Cannot access qoldiq management

---

## 🔑 Login Process

1. Go to: `http://127.0.0.1:8000/login`
2. Enter email and password
3. Enter CAPTCHA code (visible in the image)
4. Click "Кириш" (Login)

### CAPTCHA Features
- **Auto-generated** 6-character code
- **Refresh button** to get a new code
- **Case-insensitive** verification
- **Session-based** for security

---

## 🛡️ Security Features

### 1. Authentication
- Laravel's built-in authentication
- Session-based login
- "Remember me" functionality
- CAPTCHA protection

### 2. Authorization
- Role-based middleware (`CheckUserRole`)
- Automatic district filtering
- Route protection
- Active status check

### 3. Password Security
- Bcrypt hashing
- Minimum complexity requirements
- Should be changed after first login in production

---

## 📊 Data Filtering

### Super Admin
- Sees **ALL** districts
- No automatic filtering
- Can filter manually if needed

### District Users
- **Automatic filtering** by their district
- Applied to:
  - All list views
  - Statistics pages
  - Monitoring dashboards
  - Reports and exports
  - Detail pages

---

## 🚀 Implementation Status

### ✅ Completed
1. **Migration**: Added role, tuman, can_edit, is_active columns to users table
2. **User Model**: Enhanced with helper methods (isSuperAdmin(), canEdit(), etc.)
3. **Authentication Controller**: LoginController with CAPTCHA
4. **Middleware**: CheckUserRole for authorization
5. **Login Page**: Professional UI with CAPTCHA
6. **Routes**: Protected with auth and role middleware
7. **Layout**: User info display and logout button
8. **Seeder**: UserSeeder creates all users

### ⏳ TODO (Next Steps)
1. **Controller Updates**: Add district filtering logic to controllers
2. **Service Updates**: Apply filtering in YerSotuvService methods
3. **List Blade**: Show all statuses by default, preserve filters from links
4. **Edit Permissions**: Hide edit buttons for district users
5. **Production Setup**: Change default passwords

---

## 🔧 Technical Details

### Routes Structure
```php
// Public routes
GET  /login              - Show login form
POST /login              - Process login
GET  /captcha/image      - Generate CAPTCHA image
POST /captcha/refresh    - Refresh CAPTCHA

// Protected routes (require auth)
Route::middleware(['auth', 'role'])->group(function () {
    // All users
    GET  /                      - Monitoring dashboard
    GET  /umumiy                - Statistics
    GET  /ruyxat                - List view
    GET  /svod3                 - Payment monitoring
    GET  /yigma-malumot         - Summary data
    GET  /yer/{lot_raqami}      - View details
    
    // Super Admin only
    GET  /yer/create            - Create form
    POST /yer                   - Store new record
    GET  /yer/{lot}/edit        - Edit form
    PUT  /yer/{lot}             - Update record
    GET  /qoldiq                - Manage qoldiq
});
```

### Middleware Usage
```php
// Require authentication
->middleware('auth')

// Require specific role
->middleware('role:super_admin')

// Multiple roles allowed
->middleware('role:super_admin,district')
```

### User Model Methods
```php
$user->isSuperAdmin()        // Returns bool
$user->isDistrict()          // Returns bool
$user->canEdit()             // Returns bool
$user->getDistrictFilter()   // Returns district name or null
```

---

## 📝 Usage Examples

### In Controllers
```php
// Check role
if (auth()->user()->isSuperAdmin()) {
    // Admin logic
} else {
    // District logic
}

// Get district filter
$tuman = auth()->user()->getDistrictFilter();
if ($tuman) {
    $query->where('tuman', $tuman);
}
```

### In Blade Templates
```blade
@auth
    <p>Welcome, {{ auth()->user()->name }}</p>
    
    @if(auth()->user()->isSuperAdmin())
        <button>Edit</button>
    @endif
@endauth
```

---

## 🔄 Database Migration

Run these commands to set up:
```bash
# Run migration
php artisan migrate

# Seed users
php artisan db:seed --class=UserSeeder
```

---

## ⚠️ Important Notes

### Production Checklist
- [ ] Change all default passwords
- [ ] Set up SSL/HTTPS
- [ ] Configure session security
- [ ] Set up backup authentication
- [ ] Enable rate limiting
- [ ] Configure logging
- [ ] Test all user permissions
- [ ] Document custom passwords

### Security Recommendations
1. **Change passwords immediately** after deployment
2. Use **strong passwords** (min 12 chars, mixed case, numbers, symbols)
3. Enable **two-factor authentication** (future enhancement)
4. Regularly **audit user access**
5. **Monitor login attempts**
6. Set up **password reset** functionality

---

## 🐛 Troubleshooting

### Login Issues
**Problem**: Cannot login
**Solutions**:
1. Check email spelling
2. Ensure caps lock is off
3. Verify CAPTCHA is correct
4. Check if account is active
5. Clear browser cache/cookies

### Permission Issues
**Problem**: "Access denied" error
**Solutions**:
1. Verify user role in database
2. Check middleware on route
3. Ensure user is active (`is_active = 1`)
4. Check route protection

### CAPTCHA Issues
**Problem**: CAPTCHA not showing
**Solutions**:
1. Ensure GD library is enabled in PHP
2. Check session is working
3. Verify route is accessible
4. Check browser console for errors

---

## 📞 Support

For issues or questions:
- **System Admin**: admin@toshkentinvest.uz
- **Technical Support**: Check application logs in `storage/logs/`

---

**Last Updated**: December 5, 2025
**Version**: 1.0
**Author**: Qoder AI Assistant
