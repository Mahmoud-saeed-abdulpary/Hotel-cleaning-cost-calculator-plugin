=== Hotel Cleaning Calculator PRO ===
Contributors: yourname
Donate link: https://yourwebsite.com/donate
Tags: calculator, hotel, cleaning, pricing, quote, calculator plugin
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Professional hotel cleaning cost calculator with advanced pricing logic, quote management, conditional discounts, and complete UI customization.

== Description ==

**Hotel Cleaning Calculator PRO** is a comprehensive WordPress plugin that enables hotels, cleaning services, and facility management companies to provide instant, accurate cleaning cost estimates to their clients.

= 🎯 Key Features =

**Core Calculator:**
* Dynamic room management (add/remove rooms on the fly)
* Multiple room types with custom pricing
* Area-based calculations (per square meter)
* Real-time price updates via AJAX
* Mobile-responsive design with touch support
* Fully customizable UI (colors, fonts, spacing)

**Quote Management System:**
* Complete quote request form
* Admin dashboard with filtering & search
* Quote status workflow (pending/approved/rejected)
* Client information capture
* Email notifications (admin & client)
* Export to CSV/PDF
* Custom form fields (unlimited)

**Conditional Discount Engine:**
* Multiple discount rules (unlimited)
* Date range discounts
* Day of week discounts
* Minimum area/room count conditions
* Priority system
* Stacking/non-stacking rules
* Discount codes support

**UI Customization:**
* Complete color scheme editor
* Typography controls (Google Fonts integration)
* Layout presets (compact/standard/spacious)
* Border radius, spacing, button styles
* Custom CSS injection field
* Live preview in admin panel

**Translation & Branding:**
* ALL static text customizable
* 40+ editable text fields
* Custom logo upload
* Company name and tagline
* Translatable email templates
* Multi-language ready

**Integrations:**
* **Telegram Bot** - Real-time quote notifications
* **Email/SMTP** - Custom email configuration
* **Elementor** - Native drag-and-drop widget
* **Fluent Forms** - Bi-directional integration
* **Contact Form 7** - Field mapping
* **WPForms** - Seamless connection
* **Webhooks** - Connect to any external API

**Advanced Features:**
* REST API endpoints
* Activity logging
* Mobile gesture support (swipe to delete)
* Theme presets (4 designs)
* Performance optimized
* Security hardened
* GDPR compliant

= 🚀 Perfect For =

* Hotel chains
* Cleaning service companies
* Facility management businesses
* Property management companies
* Hospitality industry
* Commercial cleaning services

= 📱 Mobile Optimized =

* Touch-friendly interface
* Swipe gestures
* Responsive on all devices
* PWA-ready structure
* Works perfectly on phones & tablets

= 🔒 Security First =

* Nonce verification on all AJAX requests
* Input sanitization & validation
* SQL injection prevention
* XSS protection
* Capability checks
* Secure file uploads

= 🎨 Shortcodes =

**Main Calculator:**
`[hotel_cleaning_calculator theme="default" show_logo="yes" show_title="yes"]`

**Quote Form:**
`[hcc_quote_form show_title="yes" redirect_url=""]`

= 📚 Documentation =

Complete documentation and video tutorials available at [yourwebsite.com/docs](https://yourwebsite.com/docs)

= 💼 Premium Support =

Need help? Contact us at support@yourwebsite.com

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "Hotel Cleaning Calculator PRO"
4. Click "Install Now" then "Activate"

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the ZIP file and click "Install Now"
5. Click "Activate Plugin"

= After Activation =

1. Go to "Cleaning Calculator" in the admin menu
2. Add your room types with pricing
3. Customize colors and text (optional)
4. Add the shortcode `[hotel_cleaning_calculator]` to any page
5. Test the calculator on the frontend

== Frequently Asked Questions ==

= How do I add the calculator to my website? =

Simply add the shortcode `[hotel_cleaning_calculator]` to any page, post, or widget area. You can also use the Elementor widget if you're using Elementor.

= Can I customize the colors and design? =

Yes! Go to Cleaning Calculator > UI Customization in your admin panel. You can change colors, fonts, spacing, borders, and even add custom CSS.

= How do I set up room types? =

Navigate to Cleaning Calculator > Room Types in your admin menu. Click "Add New Room Type" and enter the name, description, and price per square meter.

= Can I create discount rules? =

Yes! Go to Cleaning Calculator > Discounts. You can create unlimited discount rules based on dates, room count, area, and more.

= Does it work with Elementor? =

Yes! If Elementor is installed, you'll automatically get a native "Hotel Cleaning Calculator" widget in the Elementor panel.

= Can I receive quote notifications via Telegram? =

Yes! Go to Cleaning Calculator > Integrations, enable Telegram, and enter your bot token and chat ID.

= Is it mobile responsive? =

Absolutely! The calculator is fully responsive and includes touch-optimized controls for mobile devices.

= Can I export quotes to CSV or PDF? =

Yes! In the Quotes dashboard, you can export individual quotes or bulk export to CSV/PDF.

= Does it support multiple languages? =

Yes! All text strings are customizable, and the plugin is translation-ready with .pot file included.

= How do I customize email templates? =

Go to Cleaning Calculator > Integrations > Email Settings. You can customize the email subject, message, and use placeholders like {quote_number}, {client_name}, etc.

= Can I use it with form plugins? =

Yes! The plugin integrates with Fluent Forms, Contact Form 7, and WPForms. Map calculator values to form fields easily.

= Is there a REST API? =

Yes! The plugin includes REST API endpoints at `/wp-json/hcc/v1/calculate` and `/wp-json/hcc/v1/quote`.

= What happens to my data if I uninstall? =

You can choose to keep or delete data. Go to Settings > Advanced and enable "Keep data on uninstall" if you want to preserve your data.

== Screenshots ==

1. Frontend calculator with multiple rooms
2. Admin panel - Room Types management
3. Quote management dashboard
4. UI Customization panel
5. Discount rules interface
6. Elementor widget
7. Mobile responsive view
8. Email notification example
9. Integration settings
10. Translation manager

== Changelog ==

= 2.0.0 - 2024-01-12 =
* Complete rewrite with enterprise architecture
* Added quote management system
* Added conditional discount engine
* Added UI customization panel
* Added Telegram integration
* Added Elementor widget
* Added translation manager
* Added branding controls
* Added form plugin integrations
* Added CSV/PDF export
* Added activity logging
* Added mobile gesture support
* Added REST API endpoints
* Improved security and performance
* Improved mobile responsiveness
* Added 40+ customizable text fields

= 1.0.0 - 2023-06-15 =
* Initial release
* Basic calculator functionality
* Room type management
* Simple admin panel

== Upgrade Notice ==

= 2.0.0 =
Major update! Complete rewrite with enterprise features. Backup your data before upgrading. All settings will be preserved.

== Additional Info ==

**Support:** support@yourwebsite.com
**Documentation:** https://yourwebsite.com/docs
**Demo:** https://demo.yourwebsite.com/calculator
**Video Tutorials:** https://youtube.com/@yourwebsite

== Privacy Policy ==

This plugin does not collect, store, or share any personal data without explicit user consent. All quote data is stored in your WordPress database. If you enable Telegram integration, quote data will be sent to Telegram's servers. Review Telegram's privacy policy for more information.

== Credits ==

Developed by Your Name (https://yourwebsite.com)
Icons by Lucide Icons (https://lucide.dev)
Font Awesome icons (https://fontawesome.com)

== License ==

This plugin is licensed under GPLv2 or later.
http://www.gnu.org/licenses/gpl-2.0.html