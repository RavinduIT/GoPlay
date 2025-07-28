import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';
import { Badge } from '@/components/ui/badge';
import { Label } from '@/components/ui/label';
import { ArrowLeft, Save, Users, Shield, Settings, Eye } from 'lucide-react';
import { useToast } from '@/hooks/use-toast';
import { Link } from 'react-router-dom';

const AdminPermissions = () => {
  const { toast } = useToast();
  const [permissions, setPermissions] = useState({
    Player: {
      viewProfile: true,
      editProfile: true,
      bookGrounds: true,
      bookCoaches: true,
      purchaseItems: true,
      viewBookings: true,
      cancelBookings: true,
      leaveReviews: true,
      uploadDocuments: false,
      accessAnalytics: false,
      manageUsers: false,
      moderateContent: false
    },
    Coach: {
      viewProfile: true,
      editProfile: true,
      bookGrounds: true,
      bookCoaches: true,
      purchaseItems: true,
      viewBookings: true,
      cancelBookings: true,
      leaveReviews: true,
      uploadDocuments: true,
      accessAnalytics: true,
      manageUsers: false,
      moderateContent: false,
      manageSchedule: true,
      setAvailability: true,
      viewStudents: true,
      createTrainingPlans: true
    },
    'Shop Owner': {
      viewProfile: true,
      editProfile: true,
      bookGrounds: true,
      bookCoaches: true,
      purchaseItems: true,
      viewBookings: true,
      cancelBookings: true,
      leaveReviews: true,
      uploadDocuments: true,
      accessAnalytics: true,
      manageUsers: false,
      moderateContent: false,
      manageInventory: true,
      processOrders: true,
      viewSales: true,
      manageShipping: true
    },
    'Complex Owner': {
      viewProfile: true,
      editProfile: true,
      bookGrounds: true,
      bookCoaches: true,
      purchaseItems: true,
      viewBookings: true,
      cancelBookings: true,
      leaveReviews: true,
      uploadDocuments: true,
      accessAnalytics: true,
      manageUsers: false,
      moderateContent: false,
      manageGrounds: true,
      setGroundAvailability: true,
      viewGroundBookings: true,
      managePricing: true
    }
  });

  const permissionCategories = {
    'Basic Access': ['viewProfile', 'editProfile'],
    'Booking & Shopping': ['bookGrounds', 'bookCoaches', 'purchaseItems', 'viewBookings', 'cancelBookings'],
    'Community': ['leaveReviews', 'moderateContent'],
    'Business': ['uploadDocuments', 'accessAnalytics'],
    'Administrative': ['manageUsers'],
    'Coach Specific': ['manageSchedule', 'setAvailability', 'viewStudents', 'createTrainingPlans'],
    'Shop Specific': ['manageInventory', 'processOrders', 'viewSales', 'manageShipping'],
    'Complex Specific': ['manageGrounds', 'setGroundAvailability', 'viewGroundBookings', 'managePricing']
  };

  const permissionLabels = {
    viewProfile: 'View Profile',
    editProfile: 'Edit Profile',
    bookGrounds: 'Book Sports Grounds',
    bookCoaches: 'Book Coaches',
    purchaseItems: 'Purchase Items',
    viewBookings: 'View Bookings',
    cancelBookings: 'Cancel Bookings',
    leaveReviews: 'Leave Reviews',
    uploadDocuments: 'Upload Documents',
    accessAnalytics: 'Access Analytics',
    manageUsers: 'Manage Users',
    moderateContent: 'Moderate Content',
    manageSchedule: 'Manage Schedule',
    setAvailability: 'Set Availability',
    viewStudents: 'View Students',
    createTrainingPlans: 'Create Training Plans',
    manageInventory: 'Manage Inventory',
    processOrders: 'Process Orders',
    viewSales: 'View Sales Reports',
    manageShipping: 'Manage Shipping',
    manageGrounds: 'Manage Grounds',
    setGroundAvailability: 'Set Ground Availability',
    viewGroundBookings: 'View Ground Bookings',
    managePricing: 'Manage Pricing'
  };

  const handleSave = () => {
    localStorage.setItem('adminPermissions', JSON.stringify(permissions));
    toast({
      title: "Permissions Updated",
      description: "User role permissions have been saved successfully.",
    });
  };

  const updatePermission = (role: string, permission: string, value: boolean) => {
    setPermissions(prev => ({
      ...prev,
      [role]: {
        ...prev[role],
        [permission]: value
      }
    }));
  };

  const toggleAllPermissions = (role: string, enabled: boolean) => {
    setPermissions(prev => ({
      ...prev,
      [role]: Object.fromEntries(
        Object.keys(prev[role]).map(key => [key, enabled])
      )
    }));
  };

  const getPermissionCount = (role: string) => {
    return Object.values(permissions[role]).filter(Boolean).length;
  };

  const getTotalPermissions = (role: string) => {
    return Object.keys(permissions[role]).length;
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
            User Permissions
          </h1>
          <p className="text-xl text-primary-foreground/90">
            Configure permissions for different user roles
          </p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-12">
        <div className="space-y-6">
          {Object.entries(permissions).map(([role, rolePermissions]) => (
            <Card key={role}>
              <CardHeader>
                <div className="flex items-center justify-between">
                  <CardTitle className="flex items-center gap-2">
                    <Users className="h-5 w-5" />
                    {role}
                    <Badge variant="secondary">
                      {getPermissionCount(role)} / {getTotalPermissions(role)} permissions
                    </Badge>
                  </CardTitle>
                  <div className="flex gap-2">
                    <Button 
                      variant="outline" 
                      size="sm"
                      onClick={() => toggleAllPermissions(role, true)}
                    >
                      Enable All
                    </Button>
                    <Button 
                      variant="outline" 
                      size="sm"
                      onClick={() => toggleAllPermissions(role, false)}
                    >
                      Disable All
                    </Button>
                  </div>
                </div>
              </CardHeader>
              <CardContent>
                <div className="space-y-6">
                  {Object.entries(permissionCategories).map(([category, categoryPermissions]) => {
                    const relevantPermissions = categoryPermissions.filter(perm => 
                      rolePermissions.hasOwnProperty(perm)
                    );
                    
                    if (relevantPermissions.length === 0) return null;

                    return (
                      <div key={category}>
                        <h4 className="text-sm font-medium text-muted-foreground mb-3 flex items-center gap-2">
                          {category === 'Basic Access' && <Eye className="h-4 w-4" />}
                          {category === 'Administrative' && <Shield className="h-4 w-4" />}
                          {category === 'Business' && <Settings className="h-4 w-4" />}
                          {category}
                        </h4>
                        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                          {relevantPermissions.map(permission => (
                            <div key={permission} className="flex items-center justify-between p-3 border rounded-lg">
                              <div>
                                <Label className="text-sm font-medium">
                                  {permissionLabels[permission]}
                                </Label>
                              </div>
                              <Switch
                                checked={rolePermissions[permission]}
                                onCheckedChange={(checked) => updatePermission(role, permission, checked)}
                              />
                            </div>
                          ))}
                        </div>
                      </div>
                    );
                  })}
                </div>
              </CardContent>
            </Card>
          ))}

          {/* Save Button */}
          <div className="flex justify-end">
            <Button onClick={handleSave} className="bg-gradient-primary">
              <Save className="h-4 w-4 mr-2" />
              Save All Permissions
            </Button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AdminPermissions;