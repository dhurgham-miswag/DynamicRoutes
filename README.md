# 🔥 DynamicRoutes

A Laravel-based API service management platform that allows you to dynamically manage API routes through a web interface without code changes.

## 🎯 What This Project Does

This is an **API Gateway with Dynamic Route Management** that provides:

- **🔄 Dynamic Route Management**: Add/remove API routes through admin panel without touching code
- **🎛️ Admin Interface**: Filament-based admin panel for route management

## 🏗️ Core Features

### ✅ Dynamic Route System
- **Database-driven routes**: Store route configurations in database
- **Real-time registration**: Routes are registered on application boot
- **Service grouping**: Organize routes by service (YouTube, Spotify, etc.)
- **Active/Inactive states**: Enable/disable routes without deletion
- **Controller Auto-Discovery**: Dynamic controller detection from file system
- **Method Auto-Discovery**: Automatic method detection from controller classes

### ✅ Admin Interface
- **Route Management**: Add, edit, delete routes through web interface
- **Smart Form Fields**: Progressive form with dependency-based field enabling
- **Controller Search**: Searchable dropdown for available controllers
- **Method Auto-Population**: Automatic method detection from selected controllers


### Usage

1. **Access Admin Panel**
2. **Create API Routes**: 
   - Go to "API Routes" section
   - Click "Create" to add new routes
   - Configure route parameters (progressive form):
     - **Middleware** (e.g., `api`, `web`) - Required first
     - **Service Group** (e.g., `youtube`) - Enabled after middleware selection
     - **Controller Name** - Searchable dropdown with available controllers
     - **Route Name** (e.g., `search`) - Enabled after controller selection
     - **Method Name** - Auto-populated from selected controller
     - **HTTP Method** (e.g., `POST`)

3. **Routes Automatically Available**: 
   ```bash
   POST /api/youtube/search
   {
       "query": "music video"
   }
   ```

## 🖥️ Admin Interface Screenshots

### API Routes List View
![API Routes List](https://github.com/dhurgham-miswag/DynamicRoutes/assets/your-username/api-routes-list.png)

*The admin panel showing all configured API routes with their middleware, service groups, controllers, methods, and active status. Features include search functionality, filtering, and bulk operations.*

### Create New API Route
![Create API Route](https://github.com/dhurgham-miswag/DynamicRoutes/assets/your-username/create-api-route.png)

*The progressive form for creating new API routes, showing method auto-population from selected controllers. The form uses dependency-based validation where fields are enabled progressively.*

## 🔄 How It Works

### Dynamic Route Registration
1. **Database Storage**: Route configurations stored in `api_routes` table
2. **Boot Registration**: Routes registered on application boot via `DynamicRouteService`
3. **Controller Resolution**: Dynamic controller class loading with case-insensitive matching
4. **Route Registration**: Laravel Route facade registration
5. **Form Validation**: Progressive form with dependency-based field enabling


## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

---

**🔥 Built with Laravel, Filament, and ❤️** 
