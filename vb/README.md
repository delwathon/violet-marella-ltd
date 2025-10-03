# Violet Marella Limited - Business Management Suite

A comprehensive multi-page business management application built with HTML, Bootstrap 5, and vanilla JavaScript. This application manages three distinct sub-businesses: Gift Store, Mini Supermarket, and Music Studio with Instrument Rental services.

![Violet Marella Limited](assets/images/logos/logo-primary.svg)

## 🚀 Features

### 🎁 **Gift Store Management**
- Complete inventory management system
- Product categorization and search
- Low stock alerts and reorder notifications
- Sales tracking and reporting
- Supplier management

### 🛒 **Mini Supermarket POS**
- Full Point of Sale system
- Barcode scanning support
- Multiple payment methods (Cash, Card, Transfer, Split)
- Receipt generation and printing
- Real-time inventory updates

### 🎵 **Music Studio Operations**
- Time-based billing system
- Customer check-in/check-out with QR codes
- Studio room availability tracking
- Session duration monitoring
- Automatic billing calculation: `extra_time_fee = (base_amount / base_time) * extra_minutes`

### 🎸 **Instrument Rental Service**
- Musical instrument booking system
- Customer database management
- Rental calendar with availability tracking
- Due date reminders and notifications
- Damage assessment and security deposits

### 📊 **Analytics & Reporting**
- Business performance dashboards
- Revenue trends and analytics
- Custom report generation
- Data export capabilities (CSV, PDF, Excel)
- Multi-business unit comparisons

### 👥 **User Management**
- Role-based access control
- User account management
- Activity logging and monitoring
- Security settings and permissions
- Multi-level authentication

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, Bootstrap 5.3.2
- **JavaScript**: Vanilla ES6+ (No frameworks)
- **Icons**: Font Awesome 6.4.0
- **Fonts**: Google Fonts (Inter)
- **Charts**: Chart.js 3.9.1
- **Storage**: Browser Local Storage / Session Storage

## 📁 Project Structure

```
violet-marella-app/
├── 📄 index.html                    # Authentication page
├── 📄 dashboard.html                # Main business dashboard
├── 📄 gift-store.html              # Inventory management
├── 📄 supermarket.html             # POS system
├── 📄 music-studio.html            # Studio management
├── 📄 instrument-rental.html       # Rental system
├── 📄 reports.html                 # Analytics & reports
├── 📄 settings.html                # System configuration
├── 📄 users.html                   # User management
├── 📁 assets/
│   ├── 📁 css/
│   │   ├── styles.css              # Main stylesheet
│   │   ├── login.css               # Login specific styles
│   │   ├── dashboard.css           # Dashboard styles
│   │   ├── gift-store.css          # Gift store styles
│   │   ├── music-studio.css        # Studio styles
│   │   └── [additional CSS files]
│   ├── 📁 js/
│   │   ├── common.js               # Shared functionality
│   │   ├── auth.js                 # Authentication logic
│   │   ├── dashboard.js            # Dashboard functionality
│   │   ├── gift-store.js           # Inventory management
│   │   ├── music-studio.js         # Studio operations
│   │   ├── supermarket.js          # POS system
│   │   └── [additional JS files]
│   └── 📁 images/
│       ├── logos/                  # Company branding
│       ├── products/               # Product images
│       ├── placeholders/           # Default images
│       └── [additional folders]
├── 📄 PROJECT_STRUCTURE.md         # Detailed documentation
└── 📄 README.md                    # This file
```

## 🚦 Getting Started

### Prerequisites
- Modern web browser (Chrome 80+, Firefox 75+, Safari 13+, Edge 80+)
- Local web server (required for proper functionality)

### Installation

1. **Clone or Download** the project files
2. **Set up a local web server**:

   ```bash
   # Using Python 3
   python -m http.server 8000
   
   # Using Python 2
   python -SimpleHTTPServer 8000
   
   # Using Node.js http-server
   npx http-server
   
   # Using PHP
   php -S localhost:8000
   
   # Using Live Server (VS Code Extension)
   # Install Live Server extension and right-click index.html -> "Open with Live Server"
   ```

3. **Open your browser** and navigate to:
   ```
   http://localhost:8000/index.html
   ```

### Demo Credentials

| Role | Email | Password | Permissions |
|------|-------|----------|-------------|
| **Administrator** | admin@violetmarella.com | admin123 | Full access to all modules |
| **Manager** | manager@violetmarella.com | manager123 | Business operations, reports |
| **Staff** | staff@violetmarella.com | staff123 | Basic operations, limited access |

## 💼 Business Modules

### Gift Store
- **Inventory Management**: Add, edit, delete products
- **Category Organization**: Seasonal, Cards, Flowers, Toys, Accessories
- **Stock Monitoring**: Real-time stock levels with alerts
- **Search & Filter**: Advanced product search capabilities

### Supermarket POS
- **Product Scanning**: Barcode support and manual entry
- **Shopping Cart**: Add, remove, modify quantities
- **Payment Processing**: Multiple payment methods
- **Receipt Generation**: Automatic receipt creation and printing

### Music Studio
- **Room Management**: 4 studio rooms with real-time status
- **Time Tracking**: Automatic session duration calculation
- **QR Code System**: Customer check-in/check-out via QR codes
- **Billing Engine**: Configurable rates with overtime calculation

### Instrument Rental
- **Equipment Inventory**: Guitars, keyboards, drums, brass, strings
- **Booking Calendar**: Visual availability tracking
- **Customer Profiles**: Rental history and contact information
- **Due Date Management**: Automated reminders and notifications

## 🎨 Design System

### Color Palette
- **Primary Violet**: #6f42c1
- **Secondary Violet**: #8b5cf6
- **Light Violet**: #f3f0ff
- **Dark Violet**: #4c1d95
- **Accent Gold**: #fbbf24

### Typography
- **Font Family**: Inter (Google Fonts)
- **Weights**: 300, 400, 500, 600, 700

### Components
- **Cards**: Rounded corners, subtle shadows
- **Buttons**: Gradient backgrounds, smooth transitions
- **Forms**: Floating labels, validation states
- **Tables**: Hover effects, responsive design

## 🔧 Configuration

### Business Settings
- **Time Zone**: Configurable timezone settings
- **Currency**: Multi-currency support (default: Nigerian Naira)
- **Tax Rates**: Configurable VAT/tax percentages
- **Business Hours**: Operating hours configuration

### Payment Methods
- **Cash Payments**: Manual entry with change calculation
- **Card Payments**: Credit/debit card processing simulation
- **Mobile Transfers**: Bank transfer integration
- **Split Payments**: Multiple payment method support

### User Roles & Permissions
- **Administrator**: Full system access
- **Manager**: Business operations and reporting
- **Staff**: Limited operational access
- **Custom Roles**: Configurable permission sets

## 📊 Analytics & Reporting

### Dashboard Metrics
- **Revenue Tracking**: Real-time and historical revenue data
- **Transaction Analytics**: Sales volume and customer metrics
- **Inventory Insights**: Stock levels and turnover rates
- **Performance KPIs**: Business unit comparisons

### Report Types
- **Sales Reports**: Revenue, transactions, customer data
- **Inventory Reports**: Stock levels, reorder points, valuations
- **Customer Reports**: Demographics, purchase history, loyalty
- **Financial Reports**: Profit margins, expenses, tax summaries

### Export Options
- **CSV**: Spreadsheet-compatible data export
- **PDF**: Formatted report documents
- **Excel**: Advanced spreadsheet with formulas
- **JSON**: Raw data for system integration

## 🔐 Security Features

### Authentication
- **Secure Login**: Email/password authentication
- **Session Management**: Automatic timeout and renewal
- **Remember Me**: Persistent login option
- **Password Policies**: Configurable strength requirements

### Access Control
- **Role-Based Permissions**: Granular access control
- **Activity Logging**: User action tracking
- **Session Monitoring**: Active session management
- **Data Protection**: Input validation and sanitization

## 📱 Responsive Design

### Mobile Support
- **Touch-Friendly**: Large buttons and touch targets
- **Responsive Layout**: Adaptive design for all screen sizes
- **Mobile POS**: Optimized point-of-sale interface
- **Offline Capability**: Basic functionality without internet

### Browser Compatibility
- **Modern Browsers**: Chrome, Firefox, Safari, Edge
- **Mobile Browsers**: iOS Safari, Chrome Mobile
- **Progressive Enhancement**: Graceful degradation
- **Accessibility**: ARIA labels and keyboard navigation

## 🚀 Performance Optimization

### Loading Speed
- **Minified Assets**: Compressed CSS and JavaScript
- **Image Optimization**: WebP format and lazy loading
- **Code Splitting**: Modular JavaScript architecture
- **Caching Strategy**: Browser caching optimization

### Memory Management
- **Efficient DOM**: Minimal DOM manipulation
- **Event Cleanup**: Proper event listener management
- **Data Pagination**: Large dataset handling
- **Garbage Collection**: Memory leak prevention

## 🔄 Data Management

### Storage Strategy
- **Local Storage**: User preferences and settings
- **Session Storage**: Temporary transaction data
- **In-Memory**: Real-time operation data
- **Export/Import**: Data backup and migration

### Backup System
- **Automatic Backups**: Scheduled data backups
- **Manual Export**: On-demand data export
- **Cloud Integration**: Optional cloud storage
- **Data Recovery**: Backup restoration tools

## 🧪 Testing

### Browser Testing
```bash
# Test in multiple browsers
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
```

### Feature Testing
- **Authentication Flow**: Login/logout functionality
- **Business Operations**: All CRUD operations
- **Payment Processing**: All payment methods
- **Report Generation**: All report types
- **User Management**: Role and permission system

### Performance Testing
- **Load Testing**: Multiple concurrent users
- **Memory Testing**: Extended usage sessions
- **Mobile Testing**: Touch interface responsiveness
- **Offline Testing**: Limited connectivity scenarios

## 📈 Future Enhancements

### Planned Features
- **Real-time Sync**: Multi-device synchronization
- **Advanced Analytics**: Machine learning insights
- **Mobile App**: Native mobile applications
- **API Integration**: Third-party service connections

### Technical Improvements
- **Backend Integration**: Database connectivity
- **Real-time Updates**: WebSocket implementation
- **PWA Features**: Progressive Web App capabilities
- **Advanced Security**: Enhanced authentication methods

## 🆘 Troubleshooting

### Common Issues

**Login not working:**
- Check demo credentials
- Ensure JavaScript is enabled
- Clear browser cache and cookies

**Features not loading:**
- Verify local server is running
- Check browser developer console for errors
- Ensure all files are in correct directories

**Display issues:**
- Update to latest browser version
- Check CSS file loading
- Verify Bootstrap CDN connectivity

### Support Resources
- **Documentation**: PROJECT_STRUCTURE.md
- **Code Comments**: Inline documentation
- **Console Logging**: Debug information
- **Error Handling**: User-friendly error messages

## 📄 License

This project is developed for Violet Marella Limited. All rights reserved.

## 👥 Contributing

This is a commercial project for Violet Marella Limited. For modifications or enhancements, please contact the development team.

## 📞 Contact

**Violet Marella Limited**
- Email: info@violetmarella.com
- Phone: +234 801 234 5678
- Address: 123 Business District, Ibadan, Oyo State, Nigeria

---

**Built with ❤️ for modern business management**