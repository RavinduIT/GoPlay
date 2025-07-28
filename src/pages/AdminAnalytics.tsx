import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, BarChart3, TrendingUp, Users, DollarSign, Calendar, Download } from 'lucide-react';
import { useToast } from '@/hooks/use-toast';
import { Link } from 'react-router-dom';

const AdminAnalytics = () => {
  const { toast } = useToast();
  const [timeRange, setTimeRange] = useState('30days');
  const [selectedMetric, setSelectedMetric] = useState('overview');

  const analyticsData = {
    overview: {
      totalUsers: 1247,
      activeUsers: 892,
      totalBookings: 234,
      revenue: 15420,
      growth: {
        users: 12.5,
        bookings: 8.3,
        revenue: 15.7
      }
    },
    userMetrics: {
      newRegistrations: 45,
      activeThisWeek: 523,
      retentionRate: 78.5,
      avgSessionTime: '24m 15s'
    },
    bookingMetrics: {
      totalBookings: 234,
      groundBookings: 145,
      coachBookings: 89,
      avgBookingValue: 65.85
    },
    revenueMetrics: {
      totalRevenue: 15420,
      bookingRevenue: 12340,
      shopRevenue: 3080,
      avgRevenuePerUser: 17.29
    }
  };

  const popularPages = [
    { page: '/book-ground', visits: 2847, percentage: 28.5 },
    { page: '/shop', visits: 2156, percentage: 21.6 },
    { page: '/book-coach', visits: 1934, percentage: 19.3 },
    { page: '/', visits: 1823, percentage: 18.2 },
    { page: '/profile', visits: 1240, percentage: 12.4 }
  ];

  const topPerformers = {
    coaches: [
      { name: 'Sarah Thompson', bookings: 45, rating: 4.9 },
      { name: 'Mike Johnson', bookings: 38, rating: 4.8 },
      { name: 'Lisa Chen', bookings: 34, rating: 4.7 }
    ],
    grounds: [
      { name: 'City Sports Complex', bookings: 67, rating: 4.8 },
      { name: 'Elite Tennis Courts', bookings: 52, rating: 4.6 },
      { name: 'Pro Basketball Arena', bookings: 48, rating: 4.7 }
    ],
    products: [
      { name: 'Professional Basketball', sales: 23, revenue: 1840 },
      { name: 'Tennis Racket Set', sales: 18, revenue: 1620 },
      { name: 'Football Equipment Kit', sales: 15, revenue: 1350 }
    ]
  };

  const handleExportAnalytics = () => {
    const exportData = {
      exportDate: new Date().toISOString(),
      timeRange,
      analytics: analyticsData,
      popularPages,
      topPerformers
    };
    
    const dataStr = JSON.stringify(exportData, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `analytics-report-${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    toast({
      title: "Analytics Exported",
      description: "Analytics report has been downloaded successfully.",
    });
  };

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <div className="bg-gradient-hero py-16">
        <div className="container mx-auto px-4">
          <Link to="/admin" className="inline-flex items-center text-primary-foreground hover:text-primary-foreground/80 mb-4">
            <ArrowLeft className="h-4 w-4 mr-2" />
            Back to Admin Dashboard
          </Link>
          <h1 className="text-4xl font-bold text-primary-foreground mb-4">
            Analytics Dashboard
          </h1>
          <p className="text-xl text-primary-foreground/90">
            Monitor platform performance and user insights
          </p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-12">
        {/* Controls */}
        <div className="flex flex-col sm:flex-row gap-4 mb-8">
          <Select value={timeRange} onValueChange={setTimeRange}>
            <SelectTrigger className="w-[200px]">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="7days">Last 7 Days</SelectItem>
              <SelectItem value="30days">Last 30 Days</SelectItem>
              <SelectItem value="3months">Last 3 Months</SelectItem>
              <SelectItem value="1year">Last Year</SelectItem>
            </SelectContent>
          </Select>
          
          <Select value={selectedMetric} onValueChange={setSelectedMetric}>
            <SelectTrigger className="w-[200px]">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="overview">Overview</SelectItem>
              <SelectItem value="users">User Metrics</SelectItem>
              <SelectItem value="bookings">Booking Metrics</SelectItem>
              <SelectItem value="revenue">Revenue Metrics</SelectItem>
            </SelectContent>
          </Select>

          <Button onClick={handleExportAnalytics} className="ml-auto">
            <Download className="h-4 w-4 mr-2" />
            Export Report
          </Button>
        </div>

        {/* Key Metrics */}
        <div className="grid md:grid-cols-4 gap-6 mb-8">
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                  <Users className="h-6 w-6 text-blue-600" />
                </div>
                <div>
                  <p className="text-sm text-muted-foreground">Total Users</p>
                  <p className="text-2xl font-bold">{analyticsData.overview.totalUsers.toLocaleString()}</p>
                  <div className="flex items-center text-sm">
                    <TrendingUp className="h-3 w-3 text-green-600 mr-1" />
                    <span className="text-green-600">+{analyticsData.overview.growth.users}%</span>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                  <Calendar className="h-6 w-6 text-green-600" />
                </div>
                <div>
                  <p className="text-sm text-muted-foreground">Total Bookings</p>
                  <p className="text-2xl font-bold">{analyticsData.overview.totalBookings}</p>
                  <div className="flex items-center text-sm">
                    <TrendingUp className="h-3 w-3 text-green-600 mr-1" />
                    <span className="text-green-600">+{analyticsData.overview.growth.bookings}%</span>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                  <DollarSign className="h-6 w-6 text-purple-600" />
                </div>
                <div>
                  <p className="text-sm text-muted-foreground">Revenue</p>
                  <p className="text-2xl font-bold">${analyticsData.overview.revenue.toLocaleString()}</p>
                  <div className="flex items-center text-sm">
                    <TrendingUp className="h-3 w-3 text-green-600 mr-1" />
                    <span className="text-green-600">+{analyticsData.overview.growth.revenue}%</span>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                  <BarChart3 className="h-6 w-6 text-orange-600" />
                </div>
                <div>
                  <p className="text-sm text-muted-foreground">Active Users</p>
                  <p className="text-2xl font-bold">{analyticsData.overview.activeUsers}</p>
                  <p className="text-sm text-muted-foreground">
                    {((analyticsData.overview.activeUsers / analyticsData.overview.totalUsers) * 100).toFixed(1)}% of total
                  </p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <div className="grid lg:grid-cols-2 gap-6 mb-8">
          {/* Popular Pages */}
          <Card>
            <CardHeader>
              <CardTitle>Popular Pages</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {popularPages.map((page, index) => (
                  <div key={index} className="flex items-center justify-between">
                    <div>
                      <p className="font-medium">{page.page}</p>
                      <p className="text-sm text-muted-foreground">{page.visits.toLocaleString()} visits</p>
                    </div>
                    <Badge variant="secondary">{page.percentage}%</Badge>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          {/* Top Performing Coaches */}
          <Card>
            <CardHeader>
              <CardTitle>Top Performing Coaches</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {topPerformers.coaches.map((coach, index) => (
                  <div key={index} className="flex items-center justify-between">
                    <div>
                      <p className="font-medium">{coach.name}</p>
                      <p className="text-sm text-muted-foreground">{coach.bookings} bookings</p>
                    </div>
                    <Badge variant="secondary">⭐ {coach.rating}</Badge>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>

        <div className="grid lg:grid-cols-2 gap-6">
          {/* Top Grounds */}
          <Card>
            <CardHeader>
              <CardTitle>Top Performing Grounds</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {topPerformers.grounds.map((ground, index) => (
                  <div key={index} className="flex items-center justify-between">
                    <div>
                      <p className="font-medium">{ground.name}</p>
                      <p className="text-sm text-muted-foreground">{ground.bookings} bookings</p>
                    </div>
                    <Badge variant="secondary">⭐ {ground.rating}</Badge>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>

          {/* Top Products */}
          <Card>
            <CardHeader>
              <CardTitle>Best Selling Products</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                {topPerformers.products.map((product, index) => (
                  <div key={index} className="flex items-center justify-between">
                    <div>
                      <p className="font-medium">{product.name}</p>
                      <p className="text-sm text-muted-foreground">{product.sales} sales</p>
                    </div>
                    <Badge variant="secondary">${product.revenue}</Badge>
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default AdminAnalytics;