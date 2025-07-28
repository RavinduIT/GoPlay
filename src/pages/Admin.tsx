import { useState, useEffect } from 'react';
import { Users, Settings, BarChart3, Shield, UserCheck, Building, Store, Download, Bell, Wrench, Lock, FileText, AlertTriangle, Eye, CheckCircle, XCircle, Clock } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '@/components/ui/alert-dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useToast } from '@/hooks/use-toast';
import { useNavigate } from 'react-router-dom';

const Admin = () => {
  const { toast } = useToast();
  const navigate = useNavigate();
  const [isLoading, setIsLoading] = useState(false);
  const [maintenanceMode, setMaintenanceMode] = useState(false);
  const [notificationDialog, setNotificationDialog] = useState(false);
  const [applications, setApplications] = useState([]);
  const [selectedApplication, setSelectedApplication] = useState(null);
  
  const [users, setUsers] = useState([
    {
      id: 1,
      name: 'John Martinez',
      email: 'john@email.com',
      role: 'Coach',
      status: 'Active',
      joinDate: '2023-01-15',
      avatar: '👨‍💼',
      lastActive: '2 hours ago'
    },
    {
      id: 2,
      name: 'Sarah Thompson',
      email: 'sarah@email.com',
      role: 'Player',
      status: 'Active',
      joinDate: '2023-02-20',
      avatar: '👩‍💼',
      lastActive: '1 day ago'
    },
    {
      id: 3,
      name: 'Mike Johnson',
      email: 'mike@email.com',
      role: 'Shop Owner',
      status: 'Pending',
      joinDate: '2024-01-10',
      avatar: '👨‍🏫',
      lastActive: '3 days ago'
    },
    {
      id: 4,
      name: 'Lisa Chen',
      email: 'lisa@email.com',
      role: 'Complex Owner',
      status: 'Active',
      joinDate: '2023-08-05',
      avatar: '👩‍🏫',
      lastActive: '5 hours ago'
    },
    {
      id: 5,
      name: 'David Wilson',
      email: 'david@email.com',
      role: 'Coach',
      status: 'Pending',
      joinDate: '2024-01-20',
      avatar: '👨‍🏫',
      lastActive: '1 hour ago'
    },
    {
      id: 6,
      name: 'Emma Davis',
      email: 'emma@email.com',
      role: 'Shop Owner',
      status: 'Active',
      joinDate: '2023-11-15',
      avatar: '👩‍💼',
      lastActive: '4 hours ago'
    },
    {
      id: 7,
      name: 'Robert Brown',
      email: 'robert@email.com',
      role: 'Complex Owner',
      status: 'Pending',
      joinDate: '2024-01-25',
      avatar: '👨‍💼',
      lastActive: '2 days ago'
    }
  ]);

  const [stats] = useState({
    totalUsers: 1247,
    activeBookings: 89,
    totalRevenue: 15420,
    newRegistrations: 23
  });

  const [recentActivity] = useState([
    { id: 1, action: 'New user registered', user: 'Alex Wilson', time: '2 minutes ago', type: 'user' },
    { id: 2, action: 'Ground booking confirmed', user: 'City Sports Complex', time: '15 minutes ago', type: 'booking' },
    { id: 3, action: 'Coach profile updated', user: 'Sarah Thompson', time: '1 hour ago', type: 'profile' },
    { id: 4, action: 'Payment processed', user: 'Mike Johnson', time: '2 hours ago', type: 'payment' },
    { id: 5, action: 'New equipment listed', user: 'Sports Store Pro', time: '3 hours ago', type: 'shop' }
  ]);

  // Load service provider applications
  useEffect(() => {
    const loadApplications = () => {
      const storedApplications = JSON.parse(localStorage.getItem('serviceProviderApplications') || '[]');
      setApplications(storedApplications);
    };
    loadApplications();
  }, []);

  const updateUserRole = (userId: number, newRole: string) => {
    setUsers(prevUsers => 
      prevUsers.map(user => 
        user.id === userId ? { ...user, role: newRole } : user
      )
    );
    toast({
      title: "Role Updated",
      description: `User role has been updated to ${newRole}`,
    });
  };

  const updateUserStatus = (userId: number, newStatus: string) => {
    setUsers(prevUsers => 
      prevUsers.map(user => 
        user.id === userId ? { ...user, status: newStatus } : user
      )
    );
    toast({
      title: "Status Updated",
      description: `User status has been updated to ${newStatus}`,
    });
  };

  const handleGeneralSettings = () => {
    navigate('/admin/general');
  };

  const handleSecuritySettings = () => {
    navigate('/admin/security');
  };

  const handleUserPermissions = () => {
    navigate('/admin/permissions');
  };

  const handleAnalyticsSettings = () => {
    navigate('/admin/analytics');
  };

  const handleSendNotification = async (title: string, message: string) => {
    setIsLoading(true);
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    setIsLoading(false);
    setNotificationDialog(false);
    
    toast({
      title: "Notification Sent Successfully",
      description: `"${title}" has been sent to ${users.length} users.`,
    });
  };

  const handleExportData = () => {
    setIsLoading(true);
    
    // Create comprehensive export data
    const exportData = {
      exportDate: new Date().toISOString(),
      summary: {
        totalUsers: users.length,
        activeUsers: users.filter(u => u.status === 'Active').length,
        pendingUsers: users.filter(u => u.status === 'Pending').length,
        totalRevenue: stats.totalRevenue,
        activeBookings: stats.activeBookings
      },
      users: users.map(user => ({
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        status: user.status,
        joinDate: user.joinDate,
        lastActive: user.lastActive
      })),
      recentActivity: recentActivity
    };
    
    const dataStr = JSON.stringify(exportData, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `admin-export-${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    setTimeout(() => {
      setIsLoading(false);
      toast({
        title: "Export Complete",
        description: `Complete admin data exported with ${users.length} users and ${recentActivity.length} activities.`,
      });
    }, 1500);
  };

  const handleGenerateReports = async () => {
    setIsLoading(true);
    
    // Simulate report generation
    await new Promise(resolve => setTimeout(resolve, 3000));
    
    // Create a sample report
    const reportData = {
      reportDate: new Date().toISOString(),
      period: "Last 30 Days",
      metrics: {
        userGrowth: "+15.3%",
        revenue: `$${stats.totalRevenue.toLocaleString()}`,
        activeBookings: stats.activeBookings,
        conversionRate: "68.2%",
        avgSessionDuration: "24m 18s"
      },
      topPerformers: {
        bestCoach: "Sarah Thompson",
        mostBookedGround: "City Sports Complex", 
        topSellingItem: "Professional Basketball"
      },
      recommendations: [
        "Increase marketing for tennis courts",
        "Add more football coaching slots",
        "Promote basketball equipment bundle"
      ]
    };
    
    const reportStr = JSON.stringify(reportData, null, 2);
    const reportBlob = new Blob([reportStr], { type: 'application/json' });
    const url = URL.createObjectURL(reportBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `admin-report-${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    setIsLoading(false);
    toast({
      title: "Reports Generated",
      description: "Complete analytics report has been generated and downloaded.",
    });
  };

  const handleSystemMaintenance = () => {
    setMaintenanceMode(!maintenanceMode);
    
    if (!maintenanceMode) {
      toast({
        title: "Maintenance Mode Activated",
        description: "System is now in maintenance mode. New registrations are disabled.",
        variant: "destructive"
      });
    } else {
      toast({
        title: "Maintenance Mode Deactivated", 
        description: "System is back online. All features are now available.",
      });
    }
  };

  const handleApplicationAction = (applicationId: string, action: 'approve' | 'reject', notes: string = '') => {
    const updatedApplications = applications.map(app => 
      app.id === applicationId 
        ? { 
            ...app, 
            status: action === 'approve' ? 'approved' : 'rejected',
            reviewedBy: 'Admin',
            reviewDate: new Date().toISOString(),
            reviewNotes: notes
          }
        : app
    );
    
    setApplications(updatedApplications);
    localStorage.setItem('serviceProviderApplications', JSON.stringify(updatedApplications));
    
    if (action === 'approve') {
      const application = applications.find(app => app.id === applicationId);
      // Convert provider type to role
      const roleMap = {
        'coach': 'Coach',
        'ground': 'Complex Owner', 
        'shop': 'Shop Owner'
      };
      
      toast({
        title: "Application Approved",
        description: `${application.personalInfo.name} has been approved as a ${roleMap[application.type]}`,
      });
    } else {
      toast({
        title: "Application Rejected",
        description: "The application has been rejected and the applicant will be notified.",
        variant: "destructive"
      });
    }
    
    setSelectedApplication(null);
  };

  const getActivityIcon = (type: string) => {
    switch (type) {
      case 'user': return <Users className="h-4 w-4" />;
      case 'booking': return <BarChart3 className="h-4 w-4" />;
      case 'profile': return <UserCheck className="h-4 w-4" />;
      case 'payment': return <Settings className="h-4 w-4" />;
      case 'shop': return <Store className="h-4 w-4" />;
      default: return <Settings className="h-4 w-4" />;
    }
  };

  const UserCard = ({ user, updateUserRole, updateUserStatus }: {
    user: any;
    updateUserRole: (userId: number, newRole: string) => void;
    updateUserStatus: (userId: number, newStatus: string) => void;
  }) => {
    const [roleDialogOpen, setRoleDialogOpen] = useState(false);
    const [statusDialogOpen, setStatusDialogOpen] = useState(false);
    const [pendingRoleChange, setPendingRoleChange] = useState<string>('');
    const [pendingStatusChange, setPendingStatusChange] = useState<string>('');

    const handleRoleChange = (newRole: string) => {
      if (newRole !== user.role) {
        setPendingRoleChange(newRole);
        setRoleDialogOpen(true);
      }
    };

    const handleStatusChange = (newStatus: string) => {
      if (newStatus !== user.status) {
        setPendingStatusChange(newStatus);
        setStatusDialogOpen(true);
      }
    };

    const confirmRoleChange = () => {
      updateUserRole(user.id, pendingRoleChange);
      setRoleDialogOpen(false);
      setPendingRoleChange('');
    };

    const confirmStatusChange = () => {
      updateUserStatus(user.id, pendingStatusChange);
      setStatusDialogOpen(false);
      setPendingStatusChange('');
    };

    return (
      <>
        <div className="flex items-center justify-between p-4 border rounded-lg">
          <div className="flex items-center gap-4">
            <Avatar>
              <AvatarImage src="/placeholder-user.jpg" alt={user.name} />
              <AvatarFallback>{user.avatar}</AvatarFallback>
            </Avatar>
            <div>
              <h3 className="font-medium text-card-foreground">{user.name}</h3>
              <p className="text-sm text-muted-foreground">{user.email}</p>
              <p className="text-xs text-muted-foreground">
                Joined {user.joinDate} • Last active {user.lastActive}
              </p>
            </div>
          </div>
          
          <div className="flex items-center gap-4">
            <div className="flex flex-col gap-2">
              <Select value={user.role} onValueChange={handleRoleChange}>
                <SelectTrigger className="w-[140px]">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Player">
                    <div className="flex items-center gap-2">
                      <Users className="h-4 w-4" />
                      Player
                    </div>
                  </SelectItem>
                  <SelectItem value="Coach">
                    <div className="flex items-center gap-2">
                      <UserCheck className="h-4 w-4" />
                      Coach
                    </div>
                  </SelectItem>
                  <SelectItem value="Shop Owner">
                    <div className="flex items-center gap-2">
                      <Store className="h-4 w-4" />
                      Shop Owner
                    </div>
                  </SelectItem>
                  <SelectItem value="Complex Owner">
                    <div className="flex items-center gap-2">
                      <Building className="h-4 w-4" />
                      Complex Owner
                    </div>
                  </SelectItem>
                </SelectContent>
              </Select>
              
              <Select value={user.status} onValueChange={handleStatusChange}>
                <SelectTrigger className="w-[140px]">
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Active">Active</SelectItem>
                  <SelectItem value="Pending">Pending</SelectItem>
                  <SelectItem value="Suspended">Suspended</SelectItem>
                  <SelectItem value="Inactive">Inactive</SelectItem>
                </SelectContent>
              </Select>
            </div>
            
            <Badge 
              variant={user.status === 'Active' ? 'secondary' : 
                      user.status === 'Pending' ? 'default' : 'destructive'}
            >
              {user.status}
            </Badge>
          </div>
        </div>

        {/* Role Change Confirmation Dialog */}
        <AlertDialog open={roleDialogOpen} onOpenChange={setRoleDialogOpen}>
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>Confirm Role Change</AlertDialogTitle>
              <AlertDialogDescription>
                Are you sure you want to change <strong>{user.name}</strong>'s role from <strong>{user.role}</strong> to <strong>{pendingRoleChange}</strong>?
                <br />
                <span className="text-sm text-muted-foreground mt-2 block">This action will immediately update their permissions and access level.</span>
              </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
              <AlertDialogCancel onClick={() => setRoleDialogOpen(false)}>
                Cancel
              </AlertDialogCancel>
              <AlertDialogAction onClick={confirmRoleChange} className="bg-primary hover:bg-primary/90">
                Yes, Change Role
              </AlertDialogAction>
            </AlertDialogFooter>
          </AlertDialogContent>
        </AlertDialog>

        {/* Status Change Confirmation Dialog */}
        <AlertDialog open={statusDialogOpen} onOpenChange={setStatusDialogOpen}>
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>Confirm Status Change</AlertDialogTitle>
              <AlertDialogDescription>
                Are you sure you want to change <strong>{user.name}</strong>'s status from <strong>{user.status}</strong> to <strong>{pendingStatusChange}</strong>?
                <br />
                {pendingStatusChange === 'Suspended' && (
                  <span className="text-sm text-red-600 mt-2 block">⚠️ This will immediately restrict their access to the platform.</span>
                )}
                {pendingStatusChange === 'Active' && user.status === 'Pending' && (
                  <span className="text-sm text-green-600 mt-2 block">✅ This will approve their registration and grant full access.</span>
                )}
              </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
              <AlertDialogCancel onClick={() => setStatusDialogOpen(false)}>
                Cancel
              </AlertDialogCancel>
              <AlertDialogAction 
                onClick={confirmStatusChange}
                className={`${pendingStatusChange === 'Suspended' ? 'bg-red-600 hover:bg-red-700' : 'bg-primary hover:bg-primary/90'}`}
              >
                Yes, Change Status
              </AlertDialogAction>
            </AlertDialogFooter>
          </AlertDialogContent>
        </AlertDialog>
      </>
    );
  };

  return (
    <div className="min-h-screen bg-background">
      {/* Header Section */}
      <div className="bg-gradient-hero py-16">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-4xl md:text-5xl font-bold text-primary-foreground mb-4">
            Admin Dashboard
          </h1>
          <p className="text-xl text-primary-foreground/90 max-w-2xl mx-auto">
            Manage users, roles, and monitor platform activity
          </p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-12">
        {/* Stats Overview */}
        <div className="grid md:grid-cols-4 gap-6 mb-8">
          <Card>
            <CardContent className="p-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center">
                  <Users className="h-6 w-6 text-primary" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-card-foreground">{stats.totalUsers.toLocaleString()}</p>
                  <p className="text-sm text-muted-foreground">Total Users</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center">
                  <BarChart3 className="h-6 w-6 text-accent" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-card-foreground">{stats.activeBookings}</p>
                  <p className="text-sm text-muted-foreground">Active Bookings</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center">
                  <Settings className="h-6 w-6 text-primary" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-card-foreground">${stats.totalRevenue.toLocaleString()}</p>
                  <p className="text-sm text-muted-foreground">Total Revenue</p>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardContent className="p-6">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 bg-accent/10 rounded-full flex items-center justify-center">
                  <UserCheck className="h-6 w-6 text-accent" />
                </div>
                <div>
                  <p className="text-2xl font-bold text-card-foreground">{stats.newRegistrations}</p>
                  <p className="text-sm text-muted-foreground">New This Week</p>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <Tabs defaultValue="users" className="space-y-6">
          <TabsList className="grid w-full grid-cols-4">
            <TabsTrigger value="users">User Management</TabsTrigger>
            <TabsTrigger value="applications">
              Service Applications
              {applications.filter(app => app.status === 'pending').length > 0 && (
                <Badge variant="destructive" className="ml-2">
                  {applications.filter(app => app.status === 'pending').length}
                </Badge>
              )}
            </TabsTrigger>
            <TabsTrigger value="activity">Recent Activity</TabsTrigger>
            <TabsTrigger value="settings">System Settings</TabsTrigger>
          </TabsList>

          <TabsContent value="users">
            <div className="space-y-6">
              {/* Active Coaches */}
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <UserCheck className="h-5 w-5" />
                    Active Coaches ({users.filter(u => u.role === 'Coach' && u.status === 'Active').length})
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {users.filter(u => u.role === 'Coach' && u.status === 'Active').map((user) => (
                      <UserCard key={user.id} user={user} updateUserRole={updateUserRole} updateUserStatus={updateUserStatus} />
                    ))}
                    {users.filter(u => u.role === 'Coach' && u.status === 'Active').length === 0 && (
                      <p className="text-center text-muted-foreground py-8">No active coaches</p>
                    )}
                  </div>
                </CardContent>
              </Card>

              {/* Active Shop Owners */}
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Store className="h-5 w-5" />
                    Active Shop Owners ({users.filter(u => u.role === 'Shop Owner' && u.status === 'Active').length})
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {users.filter(u => u.role === 'Shop Owner' && u.status === 'Active').map((user) => (
                      <UserCard key={user.id} user={user} updateUserRole={updateUserRole} updateUserStatus={updateUserStatus} />
                    ))}
                    {users.filter(u => u.role === 'Shop Owner' && u.status === 'Active').length === 0 && (
                      <p className="text-center text-muted-foreground py-8">No active shop owners</p>
                    )}
                  </div>
                </CardContent>
              </Card>

              {/* Active Ground Owners */}
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Building className="h-5 w-5" />
                    Active Ground Owners ({users.filter(u => u.role === 'Complex Owner' && u.status === 'Active').length})
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {users.filter(u => u.role === 'Complex Owner' && u.status === 'Active').map((user) => (
                      <UserCard key={user.id} user={user} updateUserRole={updateUserRole} updateUserStatus={updateUserStatus} />
                    ))}
                    {users.filter(u => u.role === 'Complex Owner' && u.status === 'Active').length === 0 && (
                      <p className="text-center text-muted-foreground py-8">No active ground owners</p>
                    )}
                  </div>
                </CardContent>
              </Card>

              {/* Regular Users (Players) */}
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Users className="h-5 w-5" />
                    Regular Users ({users.filter(u => u.role === 'Player').length})
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {users.filter(u => u.role === 'Player').map((user) => (
                      <UserCard key={user.id} user={user} updateUserRole={updateUserRole} updateUserStatus={updateUserStatus} />
                    ))}
                    {users.filter(u => u.role === 'Player').length === 0 && (
                      <p className="text-center text-muted-foreground py-8">No regular users</p>
                    )}
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          <TabsContent value="applications">
            <div className="space-y-6">
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <FileText className="h-5 w-5" />
                    Service Provider Applications
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {applications.length === 0 && (
                      <p className="text-center text-muted-foreground py-8">No applications yet</p>
                    )}
                    
                    {applications.map((application) => (
                      <div key={application.id} className="border rounded-lg p-4">
                        <div className="flex items-center justify-between mb-4">
                          <div className="flex items-center gap-3">
                            <div className="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                              {application.type === 'coach' && <UserCheck className="h-5 w-5 text-primary" />}
                              {application.type === 'ground' && <Building className="h-5 w-5 text-primary" />}
                              {application.type === 'shop' && <Store className="h-5 w-5 text-primary" />}
                            </div>
                            <div>
                              <h3 className="font-medium">{application.personalInfo.name}</h3>
                              <p className="text-sm text-muted-foreground">{application.businessInfo.businessName}</p>
                              <p className="text-xs text-muted-foreground">
                                Applied: {new Date(application.submittedDate).toLocaleDateString()}
                              </p>
                            </div>
                          </div>
                          
                          <div className="flex items-center gap-3">
                            <Badge 
                              variant={
                                application.status === 'pending' ? 'default' :
                                application.status === 'approved' ? 'secondary' : 'destructive'
                              }
                            >
                              {application.status === 'pending' && <Clock className="h-3 w-3 mr-1" />}
                              {application.status === 'approved' && <CheckCircle className="h-3 w-3 mr-1" />}
                              {application.status === 'rejected' && <XCircle className="h-3 w-3 mr-1" />}
                              {application.status.charAt(0).toUpperCase() + application.status.slice(1)}
                            </Badge>
                            
                            {application.status === 'pending' && (
                              <Dialog>
                                <DialogTrigger asChild>
                                  <Button size="sm" variant="outline">
                                    <Eye className="h-4 w-4 mr-1" />
                                    Review
                                  </Button>
                                </DialogTrigger>
                                <DialogContent className="max-w-4xl max-h-[80vh] overflow-y-auto">
                                  <DialogHeader>
                                    <DialogTitle>Service Provider Application Review</DialogTitle>
                                    <DialogDescription>
                                      Review the submitted application and documents
                                    </DialogDescription>
                                  </DialogHeader>
                                  
                                  <div className="space-y-6">
                                    {/* Personal Information */}
                                    <div>
                                      <h4 className="font-medium mb-3">Personal Information</h4>
                                      <div className="grid grid-cols-2 gap-4 text-sm">
                                        <div><strong>Name:</strong> {application.personalInfo.name}</div>
                                        <div><strong>Email:</strong> {application.personalInfo.email}</div>
                                        <div><strong>Phone:</strong> {application.personalInfo.phone}</div>
                                        <div><strong>Location:</strong> {application.personalInfo.location}</div>
                                      </div>
                                    </div>
                                    
                                    {/* Business Information */}
                                    <div>
                                      <h4 className="font-medium mb-3">Business Information</h4>
                                      <div className="space-y-2 text-sm">
                                        <div><strong>Business Name:</strong> {application.businessInfo.businessName}</div>
                                        <div><strong>Type:</strong> {application.type.charAt(0).toUpperCase() + application.type.slice(1)}</div>
                                        <div><strong>Experience:</strong> {application.businessInfo.experience}</div>
                                        {application.businessInfo.specialties.length > 0 && (
                                          <div>
                                            <strong>Specialties:</strong> {application.businessInfo.specialties.join(', ')}
                                          </div>
                                        )}
                                        <div><strong>Description:</strong> {application.businessInfo.description}</div>
                                      </div>
                                    </div>
                                    
                                    {/* Documents */}
                                    <div>
                                      <h4 className="font-medium mb-3">Submitted Documents</h4>
                                      <div className="space-y-3">
                                        {application.documents.photos.length > 0 && (
                                          <div>
                                            <strong>Photos ({application.documents.photos.length}):</strong>
                                            <div className="mt-1 flex gap-2 flex-wrap">
                                              {application.documents.photos.map((photo, idx) => (
                                                <Badge key={idx} variant="outline">{photo.name}</Badge>
                                              ))}
                                            </div>
                                          </div>
                                        )}
                                        
                                        {application.documents.certificates.length > 0 && (
                                          <div>
                                            <strong>Certificates ({application.documents.certificates.length}):</strong>
                                            <div className="mt-1 flex gap-2 flex-wrap">
                                              {application.documents.certificates.map((cert, idx) => (
                                                <Badge key={idx} variant="outline">{cert.name}</Badge>
                                              ))}
                                            </div>
                                          </div>
                                        )}
                                        
                                        {application.documents.idCard && (
                                          <div>
                                            <strong>ID Card:</strong>
                                            <Badge variant="outline" className="ml-2">{application.documents.idCard.name}</Badge>
                                          </div>
                                        )}
                                      </div>
                                    </div>
                                    
                                    {/* Action Buttons */}
                                    <div className="flex gap-4 pt-4 border-t">
                                      <AlertDialog>
                                        <AlertDialogTrigger asChild>
                                          <Button className="flex-1 bg-green-600 hover:bg-green-700">
                                            <CheckCircle className="h-4 w-4 mr-1" />
                                            Approve Application
                                          </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                          <AlertDialogHeader>
                                            <AlertDialogTitle>Approve Application</AlertDialogTitle>
                                            <AlertDialogDescription>
                                              Are you sure you want to approve <strong>{application.personalInfo.name}</strong>'s application as a{' '}
                                              <strong>{application.type === 'coach' ? 'Coach' : application.type === 'ground' ? 'Complex Owner' : 'Shop Owner'}</strong>?
                                              <br />
                                              This will grant them full service provider access.
                                            </AlertDialogDescription>
                                          </AlertDialogHeader>
                                          <AlertDialogFooter>
                                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                                            <AlertDialogAction 
                                              onClick={() => handleApplicationAction(application.id, 'approve')}
                                              className="bg-green-600 hover:bg-green-700"
                                            >
                                              Yes, Approve
                                            </AlertDialogAction>
                                          </AlertDialogFooter>
                                        </AlertDialogContent>
                                      </AlertDialog>
                                      
                                      <AlertDialog>
                                        <AlertDialogTrigger asChild>
                                          <Button variant="destructive" className="flex-1">
                                            <XCircle className="h-4 w-4 mr-1" />
                                            Reject Application
                                          </Button>
                                        </AlertDialogTrigger>
                                        <AlertDialogContent>
                                          <AlertDialogHeader>
                                            <AlertDialogTitle>Reject Application</AlertDialogTitle>
                                            <AlertDialogDescription>
                                              Are you sure you want to reject <strong>{application.personalInfo.name}</strong>'s application?
                                              <br />
                                              This action cannot be undone and they will be notified of the rejection.
                                            </AlertDialogDescription>
                                          </AlertDialogHeader>
                                          <AlertDialogFooter>
                                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                                            <AlertDialogAction 
                                              onClick={() => handleApplicationAction(application.id, 'reject')}
                                              className="bg-red-600 hover:bg-red-700"
                                            >
                                              Yes, Reject
                                            </AlertDialogAction>
                                          </AlertDialogFooter>
                                        </AlertDialogContent>
                                      </AlertDialog>
                                    </div>
                                  </div>
                                </DialogContent>
                              </Dialog>
                            )}
                          </div>
                        </div>
                        
                        {/* Application Summary */}
                        <div className="text-sm text-muted-foreground">
                          <p><strong>Email:</strong> {application.personalInfo.email}</p>
                          <p><strong>Documents:</strong> {application.documents.photos.length} photos, {application.documents.certificates.length} certificates{application.documents.idCard ? ', ID card' : ''}</p>
                          {application.status !== 'pending' && application.reviewDate && (
                            <p><strong>Reviewed:</strong> {new Date(application.reviewDate).toLocaleDateString()} by {application.reviewedBy}</p>
                          )}
                        </div>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>

          <TabsContent value="activity">
            <Card>
              <CardHeader>
                <CardTitle>Recent Platform Activity</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {recentActivity.map((activity) => (
                    <div key={activity.id} className="flex items-center gap-4 p-3 border rounded-lg">
                      <div className="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center">
                        {getActivityIcon(activity.type)}
                      </div>
                      <div className="flex-1">
                        <p className="font-medium text-card-foreground">{activity.action}</p>
                        <p className="text-sm text-muted-foreground">by {activity.user}</p>
                      </div>
                      <span className="text-xs text-muted-foreground">{activity.time}</span>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="settings">
            <div className="grid md:grid-cols-2 gap-6">
              <Card>
                <CardHeader>
                  <CardTitle>Platform Settings</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <Button 
                    className="w-full justify-start" 
                    variant="outline"
                    onClick={handleGeneralSettings}
                  >
                    <Settings className="mr-2 h-4 w-4" />
                    General Settings
                  </Button>
                  <Button 
                    className="w-full justify-start" 
                    variant="outline"
                    onClick={handleSecuritySettings}
                  >
                    <Shield className="mr-2 h-4 w-4" />
                    Security Settings
                  </Button>
                  <Button 
                    className="w-full justify-start" 
                    variant="outline"
                    onClick={handleUserPermissions}
                  >
                    <Users className="mr-2 h-4 w-4" />
                    User Permissions
                  </Button>
                  <Button 
                    className="w-full justify-start" 
                    variant="outline"
                    onClick={handleAnalyticsSettings}
                  >
                    <BarChart3 className="mr-2 h-4 w-4" />
                    Analytics Settings
                  </Button>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Quick Actions</CardTitle>
                </CardHeader>
                <CardContent className="space-y-4">
                  <Dialog open={notificationDialog} onOpenChange={setNotificationDialog}>
                    <DialogTrigger asChild>
                      <Button 
                        className="w-full bg-gradient-primary"
                        disabled={isLoading}
                      >
                        <Bell className="mr-2 h-4 w-4" />
                        Send System Notification
                      </Button>
                    </DialogTrigger>
                    <DialogContent>
                      <DialogHeader>
                        <DialogTitle>Send System Notification</DialogTitle>
                        <DialogDescription>
                          Send a notification to all {users.length} registered users
                        </DialogDescription>
                      </DialogHeader>
                      <div className="space-y-4 py-4">
                        <div>
                          <Label htmlFor="title">Notification Title</Label>
                          <Input
                            id="title"
                            placeholder="Enter notification title"
                            defaultValue="System Update"
                          />
                        </div>
                        <div>
                          <Label htmlFor="message">Message</Label>
                          <Textarea
                            id="message"
                            placeholder="Enter your message here..."
                            defaultValue="We have updated our system with new features and improvements."
                            rows={4}
                          />
                        </div>
                        <Button 
                          onClick={() => {
                            const title = (document.getElementById('title') as HTMLInputElement)?.value || "System Update";
                            const message = (document.getElementById('message') as HTMLTextAreaElement)?.value || "System notification";
                            handleSendNotification(title, message);
                          }}
                          className="w-full"
                          disabled={isLoading}
                        >
                          {isLoading ? "Sending..." : `Send to ${users.length} Users`}
                        </Button>
                      </div>
                    </DialogContent>
                  </Dialog>
                  
                  <Button 
                    className="w-full" 
                    variant="outline"
                    onClick={handleExportData}
                    disabled={isLoading}
                  >
                    <Download className="mr-2 h-4 w-4" />
                    {isLoading ? "Exporting..." : "Export Complete Data"}
                  </Button>
                  
                  <Button 
                    className="w-full" 
                    variant="outline"
                    onClick={handleGenerateReports}
                    disabled={isLoading}
                  >
                    <FileText className="mr-2 h-4 w-4" />
                    {isLoading ? "Generating..." : "Generate Analytics Report"}
                  </Button>
                  
                  <Button 
                    className={`w-full ${maintenanceMode ? 'bg-red-600 hover:bg-red-700' : ''}`}
                    variant={maintenanceMode ? "default" : "destructive"}
                    onClick={handleSystemMaintenance}
                  >
                    {maintenanceMode ? (
                      <>
                        <Shield className="mr-2 h-4 w-4" />
                        Exit Maintenance Mode
                      </>
                    ) : (
                      <>
                        <AlertTriangle className="mr-2 h-4 w-4" />
                        Enter Maintenance Mode
                      </>
                    )}
                  </Button>
                  
                  {maintenanceMode && (
                    <div className="text-center p-3 bg-red-50 border border-red-200 rounded-lg">
                      <p className="text-sm text-red-700 font-medium">⚠️ System is in Maintenance Mode</p>
                      <p className="text-xs text-red-600">New registrations are disabled</p>
                    </div>
                  )}
                </CardContent>
              </Card>
            </div>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
};

export default Admin;