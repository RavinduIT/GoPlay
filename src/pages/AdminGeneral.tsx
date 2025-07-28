import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Save, Settings } from 'lucide-react';
import { useToast } from '@/hooks/use-toast';
import { Link } from 'react-router-dom';

const AdminGeneral = () => {
  const { toast } = useToast();
  const [settings, setSettings] = useState({
    siteName: 'SportConnect',
    siteDescription: 'Your premier sports booking and coaching platform',
    supportEmail: 'support@sportconnect.com',
    timezone: 'UTC',
    currency: 'USD',
    enableRegistrations: true,
    enableBookings: true,
    enableShop: true,
    maxBookingDays: 30,
    maintenanceMessage: 'We are currently performing scheduled maintenance. Please check back soon.',
    termsOfService: 'By using our platform, you agree to our terms and conditions.',
    privacyPolicy: 'We respect your privacy and protect your personal information.'
  });

  const handleSave = () => {
    localStorage.setItem('adminGeneralSettings', JSON.stringify(settings));
    toast({
      title: "Settings Saved",
      description: "General platform settings have been updated successfully.",
    });
  };

  const updateSetting = (key: string, value: any) => {
    setSettings(prev => ({ ...prev, [key]: value }));
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
            General Settings
          </h1>
          <p className="text-xl text-primary-foreground/90">
            Configure basic platform settings and preferences
          </p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-12">
        <div className="grid gap-6 max-w-4xl">
          {/* Site Information */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Settings className="h-5 w-5" />
                Site Information
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid md:grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="siteName">Site Name</Label>
                  <Input
                    id="siteName"
                    value={settings.siteName}
                    onChange={(e) => updateSetting('siteName', e.target.value)}
                  />
                </div>
                <div>
                  <Label htmlFor="supportEmail">Support Email</Label>
                  <Input
                    id="supportEmail"
                    type="email"
                    value={settings.supportEmail}
                    onChange={(e) => updateSetting('supportEmail', e.target.value)}
                  />
                </div>
              </div>
              <div>
                <Label htmlFor="siteDescription">Site Description</Label>
                <Textarea
                  id="siteDescription"
                  value={settings.siteDescription}
                  onChange={(e) => updateSetting('siteDescription', e.target.value)}
                  rows={3}
                />
              </div>
            </CardContent>
          </Card>

          {/* Platform Settings */}
          <Card>
            <CardHeader>
              <CardTitle>Platform Configuration</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid md:grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="timezone">Default Timezone</Label>
                  <Select value={settings.timezone} onValueChange={(value) => updateSetting('timezone', value)}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="UTC">UTC</SelectItem>
                      <SelectItem value="EST">Eastern Time</SelectItem>
                      <SelectItem value="PST">Pacific Time</SelectItem>
                      <SelectItem value="CST">Central Time</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div>
                  <Label htmlFor="currency">Default Currency</Label>
                  <Select value={settings.currency} onValueChange={(value) => updateSetting('currency', value)}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="USD">USD ($)</SelectItem>
                      <SelectItem value="EUR">EUR (€)</SelectItem>
                      <SelectItem value="GBP">GBP (£)</SelectItem>
                      <SelectItem value="CAD">CAD (C$)</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>
              <div>
                <Label htmlFor="maxBookingDays">Maximum Booking Days in Advance</Label>
                <Input
                  id="maxBookingDays"
                  type="number"
                  value={settings.maxBookingDays}
                  onChange={(e) => updateSetting('maxBookingDays', parseInt(e.target.value))}
                  min="1"
                  max="365"
                />
              </div>
            </CardContent>
          </Card>

          {/* Feature Toggles */}
          <Card>
            <CardHeader>
              <CardTitle>Feature Controls</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <Label>Enable User Registrations</Label>
                  <p className="text-sm text-muted-foreground">Allow new users to register accounts</p>
                </div>
                <Switch
                  checked={settings.enableRegistrations}
                  onCheckedChange={(checked) => updateSetting('enableRegistrations', checked)}
                />
              </div>
              <div className="flex items-center justify-between">
                <div>
                  <Label>Enable Bookings</Label>
                  <p className="text-sm text-muted-foreground">Allow users to book courts and coaches</p>
                </div>
                <Switch
                  checked={settings.enableBookings}
                  onCheckedChange={(checked) => updateSetting('enableBookings', checked)}
                />
              </div>
              <div className="flex items-center justify-between">
                <div>
                  <Label>Enable Shop</Label>
                  <p className="text-sm text-muted-foreground">Allow users to purchase sports equipment</p>
                </div>
                <Switch
                  checked={settings.enableShop}
                  onCheckedChange={(checked) => updateSetting('enableShop', checked)}
                />
              </div>
            </CardContent>
          </Card>

          {/* Legal Content */}
          <Card>
            <CardHeader>
              <CardTitle>Legal & Policies</CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div>
                <Label htmlFor="termsOfService">Terms of Service</Label>
                <Textarea
                  id="termsOfService"
                  value={settings.termsOfService}
                  onChange={(e) => updateSetting('termsOfService', e.target.value)}
                  rows={4}
                />
              </div>
              <div>
                <Label htmlFor="privacyPolicy">Privacy Policy</Label>
                <Textarea
                  id="privacyPolicy"
                  value={settings.privacyPolicy}
                  onChange={(e) => updateSetting('privacyPolicy', e.target.value)}
                  rows={4}
                />
              </div>
              <div>
                <Label htmlFor="maintenanceMessage">Maintenance Mode Message</Label>
                <Textarea
                  id="maintenanceMessage"
                  value={settings.maintenanceMessage}
                  onChange={(e) => updateSetting('maintenanceMessage', e.target.value)}
                  rows={3}
                />
              </div>
            </CardContent>
          </Card>

          {/* Save Button */}
          <div className="flex justify-end">
            <Button onClick={handleSave} className="bg-gradient-primary">
              <Save className="h-4 w-4 mr-2" />
              Save Settings
            </Button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AdminGeneral;