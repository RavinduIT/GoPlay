import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ArrowLeft, Save, Shield, Key, Lock, Eye } from 'lucide-react';
import { useToast } from '@/hooks/use-toast';
import { Link } from 'react-router-dom';

const AdminSecurity = () => {
  const { toast } = useToast();
  const [settings, setSettings] = useState({
    enableTwoFactor: false,
    passwordMinLength: 8,
    requireSpecialChars: true,
    requireNumbers: true,
    passwordExpiration: 90,
    maxLoginAttempts: 5,
    lockoutDuration: 30,
    enableCaptcha: true,
    sessionTimeout: 24,
    enableEmailVerification: true,
    allowMultipleSessions: false,
    logSecurityEvents: true,
    enableIpWhitelist: false,
    ipWhitelist: '',
    encryptionLevel: 'high'
  });

  const handleSave = () => {
    localStorage.setItem('adminSecuritySettings', JSON.stringify(settings));
    toast({
      title: "Security Settings Saved",
      description: "Security configuration has been updated successfully.",
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
            Security Settings
          </h1>
          <p className="text-xl text-primary-foreground/90">
            Configure security policies and authentication settings
          </p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-12">
        <div className="grid gap-6 max-w-4xl">
          {/* Password Policies */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Key className="h-5 w-5" />
                Password Policies
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid md:grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="passwordMinLength">Minimum Password Length</Label>
                  <Input
                    id="passwordMinLength"
                    type="number"
                    value={settings.passwordMinLength}
                    onChange={(e) => updateSetting('passwordMinLength', parseInt(e.target.value))}
                    min="6"
                    max="20"
                  />
                </div>
                <div>
                  <Label htmlFor="passwordExpiration">Password Expiration (days)</Label>
                  <Input
                    id="passwordExpiration"
                    type="number"
                    value={settings.passwordExpiration}
                    onChange={(e) => updateSetting('passwordExpiration', parseInt(e.target.value))}
                    min="30"
                    max="365"
                  />
                </div>
              </div>
              <div className="flex items-center justify-between">
                <div>
                  <Label>Require Special Characters</Label>
                  <p className="text-sm text-muted-foreground">Passwords must contain special characters</p>
                </div>
                <Switch
                  checked={settings.requireSpecialChars}
                  onCheckedChange={(checked) => updateSetting('requireSpecialChars', checked)}
                />
              </div>
              <div className="flex items-center justify-between">
                <div>
                  <Label>Require Numbers</Label>
                  <p className="text-sm text-muted-foreground">Passwords must contain at least one number</p>
                </div>
                <Switch
                  checked={settings.requireNumbers}
                  onCheckedChange={(checked) => updateSetting('requireNumbers', checked)}
                />
              </div>
            </CardContent>
          </Card>

          {/* Authentication Settings */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Lock className="h-5 w-5" />
                Authentication Settings
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <Label>Enable Two-Factor Authentication</Label>
                  <p className="text-sm text-muted-foreground">Require 2FA for all admin accounts</p>
                </div>
                <Switch
                  checked={settings.enableTwoFactor}
                  onCheckedChange={(checked) => updateSetting('enableTwoFactor', checked)}
                />
              </div>
              <div className="flex items-center justify-between">
                <div>
                  <Label>Enable Email Verification</Label>
                  <p className="text-sm text-muted-foreground">Require email verification for new accounts</p>
                </div>
                <Switch
                  checked={settings.enableEmailVerification}
                  onCheckedChange={(checked) => updateSetting('enableEmailVerification', checked)}
                />
              </div>
              <div className="flex items-center justify-between">
                <div>
                  <Label>Enable CAPTCHA</Label>
                  <p className="text-sm text-muted-foreground">Show CAPTCHA on login and registration forms</p>
                </div>
                <Switch
                  checked={settings.enableCaptcha}
                  onCheckedChange={(checked) => updateSetting('enableCaptcha', checked)}
                />
              </div>
              <div className="grid md:grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="sessionTimeout">Session Timeout (hours)</Label>
                  <Input
                    id="sessionTimeout"
                    type="number"
                    value={settings.sessionTimeout}
                    onChange={(e) => updateSetting('sessionTimeout', parseInt(e.target.value))}
                    min="1"
                    max="168"
                  />
                </div>
                <div>
                  <Label htmlFor="encryptionLevel">Encryption Level</Label>
                  <Select value={settings.encryptionLevel} onValueChange={(value) => updateSetting('encryptionLevel', value)}>
                    <SelectTrigger>
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="standard">Standard</SelectItem>
                      <SelectItem value="high">High</SelectItem>
                      <SelectItem value="maximum">Maximum</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* Access Control */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Shield className="h-5 w-5" />
                Access Control
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="grid md:grid-cols-2 gap-4">
                <div>
                  <Label htmlFor="maxLoginAttempts">Max Login Attempts</Label>
                  <Input
                    id="maxLoginAttempts"
                    type="number"
                    value={settings.maxLoginAttempts}
                    onChange={(e) => updateSetting('maxLoginAttempts', parseInt(e.target.value))}
                    min="3"
                    max="10"
                  />
                </div>
                <div>
                  <Label htmlFor="lockoutDuration">Lockout Duration (minutes)</Label>
                  <Input
                    id="lockoutDuration"
                    type="number"
                    value={settings.lockoutDuration}
                    onChange={(e) => updateSetting('lockoutDuration', parseInt(e.target.value))}
                    min="5"
                    max="1440"
                  />
                </div>
              </div>
              <div className="flex items-center justify-between">
                <div>
                  <Label>Allow Multiple Sessions</Label>
                  <p className="text-sm text-muted-foreground">Allow users to login from multiple devices</p>
                </div>
                <Switch
                  checked={settings.allowMultipleSessions}
                  onCheckedChange={(checked) => updateSetting('allowMultipleSessions', checked)}
                />
              </div>
              <div className="flex items-center justify-between">
                <div>
                  <Label>Enable IP Whitelist</Label>
                  <p className="text-sm text-muted-foreground">Restrict admin access to specific IP addresses</p>
                </div>
                <Switch
                  checked={settings.enableIpWhitelist}
                  onCheckedChange={(checked) => updateSetting('enableIpWhitelist', checked)}
                />
              </div>
              {settings.enableIpWhitelist && (
                <div>
                  <Label htmlFor="ipWhitelist">Allowed IP Addresses</Label>
                  <Input
                    id="ipWhitelist"
                    value={settings.ipWhitelist}
                    onChange={(e) => updateSetting('ipWhitelist', e.target.value)}
                    placeholder="192.168.1.1, 10.0.0.1, ..."
                  />
                  <p className="text-sm text-muted-foreground mt-1">Separate multiple IPs with commas</p>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Audit & Monitoring */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Eye className="h-5 w-5" />
                Audit & Monitoring
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="flex items-center justify-between">
                <div>
                  <Label>Log Security Events</Label>
                  <p className="text-sm text-muted-foreground">Keep detailed logs of security-related activities</p>
                </div>
                <Switch
                  checked={settings.logSecurityEvents}
                  onCheckedChange={(checked) => updateSetting('logSecurityEvents', checked)}
                />
              </div>
            </CardContent>
          </Card>

          {/* Save Button */}
          <div className="flex justify-end">
            <Button onClick={handleSave} className="bg-gradient-primary">
              <Save className="h-4 w-4 mr-2" />
              Save Security Settings
            </Button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AdminSecurity;