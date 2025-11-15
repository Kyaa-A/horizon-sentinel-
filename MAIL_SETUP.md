# Mail Configuration Guide

## Current Setup: Log Driver (Already Working!)

Your notifications are **already working** with the log driver. Emails are written to `storage/logs/laravel.log` instead of being sent.

### How to View Email Notifications

1. **Via Log File:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Via Laravel Pail** (real-time, colorized):
   ```bash
   php artisan pail
   ```

3. **Test a Notification:**
   ```bash
   php artisan tinker

   # Get a manager and employee
   $manager = User::where('role', 'manager')->first();
   $employee = User::where('role', 'employee')->first();
   $request = LeaveRequest::first();

   # Send test notification
   $manager->notify(new \App\Notifications\LeaveRequestSubmitted($request));

   # Check the log file to see the email content!
   ```

---

## Upgrade Options (When You Need Real Emails)

### Option 1: Mailtrap (Best for Development)

**Free tier:** 500 emails/month
**Website:** https://mailtrap.io

**Steps:**
1. Create free account at mailtrap.io
2. Go to "My Inbox"
3. Copy SMTP credentials
4. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@horizonsentinel.test
MAIL_FROM_NAME="Horizon Sentinel"
```

5. Clear config cache:
```bash
php artisan config:clear
```

**Benefits:**
-  View beautiful HTML emails in web interface
-  No risk of accidentally emailing real users
-  Test spam scores and deliverability
-  Inspect all email headers

---

### Option 2: Gmail (Quick Real Emails)

**Steps:**
1. Enable 2-factor authentication on your Google account
2. Go to https://myaccount.google.com/security
3. Click "2-Step Verification" ’ "App passwords"
4. Generate app password for "Mail"
5. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your.email@gmail.com
MAIL_PASSWORD=your_16_char_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your.email@gmail.com
MAIL_FROM_NAME="Horizon Sentinel"
```

6. Clear config cache:
```bash
php artisan config:clear
```

**Limits:** ~500 emails/day for free accounts

---

### Option 3: SendGrid (Production)

**Free tier:** 100 emails/day
**Paid:** $20/month for 40,000 emails
**Website:** https://sendgrid.com

**Steps:**
1. Create account at sendgrid.com
2. Verify your sender email/domain
3. Create API key in Settings ’ API Keys
4. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@horizondynamics.com
MAIL_FROM_NAME="Horizon Sentinel"
```

---

### Option 4: Amazon SES (Cheapest for Production)

**Pricing:** $0.10 per 1,000 emails
**Setup complexity:** Medium (requires AWS account)

**Steps:**
1. Create AWS account
2. Go to Amazon SES
3. Verify your sender email/domain
4. Create SMTP credentials
5. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=email-smtp.us-east-1.amazonaws.com
MAIL_PORT=587
MAIL_USERNAME=your_smtp_username
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@horizondynamics.com
MAIL_FROM_NAME="Horizon Sentinel"
```

---

## Queue Worker (Important!)

Since all notifications implement `ShouldQueue`, you need to run a queue worker:

```bash
# In development (run in a separate terminal):
php artisan queue:work

# Or use the dev script (already includes queue worker):
./start-dev.sh
# OR
composer dev
```

**For production**, use a process manager like Supervisor:
```bash
php artisan queue:work --daemon --tries=3
```

---

## Testing Notifications

### Via Tinker:
```bash
php artisan tinker

# Submit notification (to manager)
$employee = User::find(3);
$manager = User::find(1);
$request = LeaveRequest::create([
    'user_id' => $employee->id,
    'manager_id' => $manager->id,
    'leave_type' => 'vacation',
    'start_date' => '2025-12-25',
    'end_date' => '2025-12-27',
    'total_days' => 2,
    'status' => 'pending',
    'submitted_at' => now(),
]);

$manager->notify(new \App\Notifications\LeaveRequestSubmitted($request));

# Check database notifications:
DB::table('notifications')->latest()->first();
```

### Via Application:
1. Log in as an employee
2. Submit a leave request
3. Check:
   - **Log driver**: `tail -f storage/logs/laravel.log`
   - **Mailtrap**: Check your Mailtrap inbox
   - **Gmail**: Check your Gmail
   - **Database**: `DB::table('notifications')->latest()->get()`

---

## Troubleshooting

### Queue not processing?
```bash
# Make sure queue worker is running:
php artisan queue:work

# Check failed jobs:
php artisan queue:failed

# Retry failed jobs:
php artisan queue:retry all
```

### Emails not sending?
```bash
# Clear config cache:
php artisan config:clear

# Check mail configuration:
php artisan tinker
> config('mail')

# Test connection:
php artisan tinker
> Mail::raw('Test email', function($msg) {
      $msg->to('test@example.com')->subject('Test');
  });
```

### Check logs:
```bash
# Real-time log viewing:
php artisan pail

# Or traditional tail:
tail -f storage/logs/laravel.log
```

---

## Recommendation

**For now (Development):**
-  Keep `MAIL_MAILER=log` (already set)
-  View emails in `storage/logs/laravel.log`
-  Focus on building features

**When you need to test HTML rendering:**
- Upgrade to **Mailtrap** (5 minutes setup, free)

**For production:**
- Use **SendGrid** or **Amazon SES**
- Set up proper domain verification
- Monitor deliverability rates
