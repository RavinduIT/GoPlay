import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { AuthProvider } from "./contexts/AuthContext";
import Navbar from "./components/Navbar";
import Home from "./pages/Home";
import BookGround from "./pages/BookGround";
import BookCoach from "./pages/BookCoach";
import Shop from "./pages/Shop";
import ProductDetails from "./pages/ProductDetails";
import News from "./pages/News";
import UnifiedPayment from "./components/UnifiedPayment";
import PaymentSuccess from "./pages/PaymentSuccess";
import Profile from "./pages/Profile";
import Admin from "./pages/Admin";
import Login from "./pages/Login";
import Signup from "./pages/Signup";
import NotFound from "./pages/NotFound";
import AdminShop from "./pages/AdminShop";
import AdminGround from "./pages/AdminGround";
import AdminCoach from "./pages/AdminCoach";
import GroundDetails from "./pages/GroundDetails";
import ServiceProviderRegister from "./pages/ServiceProviderRegister";
import Cart from "./pages/Cart";
import NewsDetails from "./pages/NewsDetails";
import CoachProfile from "./pages/CoachProfile";
import MyBookings from "./pages/MyBookings";
import AdminGeneral from "./pages/AdminGeneral";
import AdminSecurity from "./pages/AdminSecurity";
import AdminPermissions from "./pages/AdminPermissions";
import AdminAnalytics from "./pages/AdminAnalytics";

const queryClient = new QueryClient();

const App = () => (
  <QueryClientProvider client={queryClient}>
    <TooltipProvider>
      <Toaster />
      <Sonner />
      <BrowserRouter>
        <AuthProvider>
          <div className="min-h-screen bg-background">
            <Navbar />
            <Routes>
              <Route path="/" element={<Home />} />
              <Route path="/login" element={<Login />} />
              <Route path="/signup" element={<Signup />} />
              <Route path="/book-ground" element={<BookGround />} />
              <Route path="/book-coach" element={<BookCoach />} />
              <Route path="/shop" element={<Shop />} />
              <Route path="/product-details" element={<ProductDetails />} />
              <Route path="/news" element={<News />} />
              <Route path="/payment" element={<UnifiedPayment />} />
              <Route path="/payment-success" element={<PaymentSuccess />} />
              <Route path="/profile" element={<Profile />} />
              <Route path="/admin" element={<Admin />} />
              <Route path="/admin/shop" element={<AdminShop />} />
              <Route path="/admin/ground" element={<AdminGround />} />
              <Route path="/admin/coach" element={<AdminCoach />} />
              <Route path="/ground-details" element={<GroundDetails />} />
              <Route path="/service-provider-register" element={<ServiceProviderRegister />} />
              <Route path="/cart" element={<Cart />} />
              <Route path="/news-details" element={<NewsDetails />} />
              <Route path="/coach-profile" element={<CoachProfile />} />
              <Route path="/my-bookings" element={<MyBookings />} />
              <Route path="/admin/general" element={<AdminGeneral />} />
              <Route path="/admin/security" element={<AdminSecurity />} />
              <Route path="/admin/permissions" element={<AdminPermissions />} />
              <Route path="/admin/analytics" element={<AdminAnalytics />} />
              {/* ADD ALL CUSTOM ROUTES ABOVE THE CATCH-ALL "*" ROUTE */}
              <Route path="*" element={<NotFound />} />
            </Routes>
          </div>
        </AuthProvider>
      </BrowserRouter>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;
