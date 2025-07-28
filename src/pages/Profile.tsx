import { useState, useEffect, useRef } from 'react';
import { User, Edit, Calendar, MapPin, Phone, Mail, Camera, Save, Upload } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { useAuth } from '@/contexts/AuthContext';
import { useToast } from '@/hooks/use-toast';
import { useNavigate } from 'react-router-dom';

const Profile = () => {
  const [isEditing, setIsEditing] = useState(false);
  const [newSport, setNewSport] = useState('');
  const [showSportInput, setShowSportInput] = useState(false);
  const [profileImage, setProfileImage] = useState<string | null>(null);
  const fileInputRef = useRef<HTMLInputElement>(null);
  const { user, updateUser, isAuthenticated } = useAuth();
  const { toast } = useToast();
  const navigate = useNavigate();

  useEffect(() => {
    if (!isAuthenticated) {
      navigate('/login');
    }
  }, [isAuthenticated, navigate]);

  if (!user) {
    return null;
  }

  const [bookingHistory] = useState([
    {
      id: 1,
      type: 'Ground',
      name: 'City Sports Complex',
      date: '2024-01-15',
      time: '14:00-16:00',
      status: 'Completed',
      amount: 50
    },
    {
      id: 2,
      type: 'Coach',
      name: 'Sarah Thompson - Tennis',
      date: '2024-01-10',
      time: '10:00-11:00',
      status: 'Completed',
      amount: 60
    },
    {
      id: 3,
      type: 'Ground',
      name: 'Basketball Arena Pro',
      date: '2024-01-20',
      time: '18:00-20:00',
      status: 'Upcoming',
      amount: 40
    }
  ]);

  const handleSave = () => {
    setIsEditing(false);
    updateUser(user);
    toast({
      title: "Profile updated",
      description: "Your profile has been successfully updated.",
    });
  };

  const handleInputChange = (field: string, value: string) => {
    updateUser({ [field]: value });
  };

  const handleAddSport = () => {
    if (newSport.trim() && !user.sports.includes(newSport.trim())) {
      const updatedSports = [...user.sports, newSport.trim()];
      updateUser({ sports: updatedSports });
      setNewSport('');
      setShowSportInput(false);
      toast({
        title: "Sport added",
        description: `${newSport.trim()} has been added to your favorite sports.`,
      });
    }
  };

  const handleRemoveSport = (sportToRemove: string) => {
    const updatedSports = user.sports.filter(sport => sport !== sportToRemove);
    updateUser({ sports: updatedSports });
    toast({
      title: "Sport removed",
      description: `${sportToRemove} has been removed from your favorite sports.`,
    });
  };

  const handleImageUpload = (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (file) {
      // Check file size (max 5MB)
      if (file.size > 5 * 1024 * 1024) {
        toast({
          title: "File too large",
          description: "Please select an image smaller than 5MB.",
          variant: "destructive"
        });
        return;
      }

      // Check file type
      if (!file.type.startsWith('image/')) {
        toast({
          title: "Invalid file type",
          description: "Please select an image file.",
          variant: "destructive"
        });
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        const imageUrl = e.target?.result as string;
        setProfileImage(imageUrl);
        updateUser({ avatar: imageUrl });
        toast({
          title: "Profile picture updated",
          description: "Your profile picture has been successfully updated.",
        });
      };
      reader.readAsDataURL(file);
    }
  };

  const triggerFileInput = () => {
    fileInputRef.current?.click();
  };

  return (
    <div className="min-h-screen bg-background">
      {/* Header Section */}
      <div className="bg-gradient-hero py-16">
        <div className="container mx-auto px-4">
          <div className="flex flex-col md:flex-row items-center gap-6">
            <div className="relative">
              <Avatar className="h-32 w-32 border-4 border-primary-foreground">
                <AvatarImage 
                  src={profileImage || `https://images.unsplash.com/photo-1535268647677-300dbf3d78d1?w=400&h=400&fit=crop&crop=faces`} 
                  alt={user.name} 
                />
                <AvatarFallback className="text-4xl">{user.avatar}</AvatarFallback>
              </Avatar>
              <Button 
                size="icon" 
                className="absolute bottom-0 right-0 rounded-full bg-primary hover:bg-primary/90 transition-colors"
                onClick={triggerFileInput}
                title="Upload profile picture"
              >
                <Camera className="h-4 w-4" />
              </Button>
              <input
                ref={fileInputRef}
                type="file"
                accept="image/*"
                onChange={handleImageUpload}
                className="hidden"
              />
            </div>
            <div className="text-center md:text-left text-primary-foreground">
              <h1 className="text-3xl md:text-4xl font-bold mb-2">{user.name}</h1>
              <div className="flex flex-col md:flex-row gap-4 mb-4">
                <div className="flex items-center gap-2">
                  <Mail className="h-4 w-4" />
                  <span>{user.email}</span>
                </div>
                <div className="flex items-center gap-2">
                  <MapPin className="h-4 w-4" />
                  <span>{user.location}</span>
                </div>
                <div className="flex items-center gap-2">
                  <Calendar className="h-4 w-4" />
                  <span>Member since {new Date(user.joinDate).getFullYear()}</span>
                </div>
              </div>
              <Badge className="bg-primary-foreground text-primary">
                {user.role}
              </Badge>
            </div>
          </div>
        </div>
      </div>

      <div className="container mx-auto px-4 py-12">
        <div className="grid lg:grid-cols-3 gap-8">
          {/* Profile Information */}
          <div className="lg:col-span-2">
            <Card>
              <CardHeader className="flex flex-row items-center justify-between">
                <CardTitle className="flex items-center gap-2">
                  <User className="h-5 w-5" />
                  Profile Information
                </CardTitle>
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => isEditing ? handleSave() : setIsEditing(true)}
                >
                  {isEditing ? (
                    <>
                      <Save className="mr-2 h-4 w-4" />
                      Save Changes
                    </>
                  ) : (
                    <>
                      <Edit className="mr-2 h-4 w-4" />
                      Edit Profile
                    </>
                  )}
                </Button>
              </CardHeader>
              <CardContent className="space-y-6">
                <div className="grid md:grid-cols-2 gap-4">
                  <div>
                    <Label htmlFor="name">Full Name</Label>
                    <Input
                      id="name"
                      value={user.name}
                      onChange={(e) => handleInputChange('name', e.target.value)}
                      disabled={!isEditing}
                      className="mt-1"
                    />
                  </div>
                  <div>
                    <Label htmlFor="email">Email</Label>
                    <Input
                      id="email"
                      type="email"
                      value={user.email}
                      onChange={(e) => handleInputChange('email', e.target.value)}
                      disabled={!isEditing}
                      className="mt-1"
                    />
                  </div>
                </div>

                <div className="grid md:grid-cols-2 gap-4">
                  <div>
                    <Label htmlFor="phone">Phone Number</Label>
                    <Input
                      id="phone"
                      value={user.phone}
                      onChange={(e) => handleInputChange('phone', e.target.value)}
                      disabled={!isEditing}
                      className="mt-1"
                    />
                  </div>
                  <div>
                    <Label htmlFor="location">Location</Label>
                    <Input
                      id="location"
                      value={user.location}
                      onChange={(e) => handleInputChange('location', e.target.value)}
                      disabled={!isEditing}
                      className="mt-1"
                    />
                  </div>
                </div>

                <div>
                  <Label htmlFor="role">Role</Label>
                  <Select
                    value={user.role}
                    onValueChange={(value) => handleInputChange('role', value)}
                    disabled={!isEditing}
                  >
                    <SelectTrigger className="mt-1">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="Player">Player</SelectItem>
                      <SelectItem value="Coach">Coach</SelectItem>
                      <SelectItem value="Shop Owner">Shop Owner</SelectItem>
                      <SelectItem value="Complex Owner">Complex Owner</SelectItem>
                    </SelectContent>
                  </Select>
                </div>

                <div>
                  <Label htmlFor="bio">Bio</Label>
                  <Textarea
                    id="bio"
                    value={user.bio}
                    onChange={(e) => handleInputChange('bio', e.target.value)}
                    disabled={!isEditing}
                    className="mt-1"
                    rows={4}
                  />
                </div>

                <div>
                  <Label>Favorite Sports</Label>
                  <div className="flex flex-wrap gap-2 mt-2">
                    {user.sports.map((sport) => (
                      <Badge 
                        key={sport} 
                        variant="secondary" 
                        className="bg-primary/10 text-primary cursor-pointer hover:bg-primary/20 transition-colors"
                        onClick={() => isEditing && handleRemoveSport(sport)}
                        title={isEditing ? "Click to remove" : undefined}
                      >
                        {sport}
                        {isEditing && (
                          <span className="ml-1 text-xs opacity-70">×</span>
                        )}
                      </Badge>
                    ))}
                    {isEditing && !showSportInput && (
                      <Button 
                        variant="outline" 
                        size="sm"
                        onClick={() => setShowSportInput(true)}
                      >
                        + Add Sport
                      </Button>
                    )}
                    {isEditing && showSportInput && (
                      <div className="flex gap-2 items-center">
                        <Input
                          value={newSport}
                          onChange={(e) => setNewSport(e.target.value)}
                          placeholder="Enter sport name"
                          className="w-32"
                          onKeyPress={(e) => {
                            if (e.key === 'Enter') {
                              handleAddSport();
                            }
                          }}
                        />
                        <Button 
                          size="sm" 
                          onClick={handleAddSport}
                          disabled={!newSport.trim()}
                        >
                          Add
                        </Button>
                        <Button 
                          size="sm" 
                          variant="outline"
                          onClick={() => {
                            setShowSportInput(false);
                            setNewSport('');
                          }}
                        >
                          Cancel
                        </Button>
                      </div>
                    )}
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Booking History & Stats */}
          <div className="space-y-6">
            {/* Quick Stats */}
            <Card>
              <CardHeader>
                <CardTitle>Quick Stats</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div className="flex justify-between">
                    <span className="text-muted-foreground">Total Bookings:</span>
                    <span className="font-bold">12</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-muted-foreground">This Month:</span>
                    <span className="font-bold">3</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-muted-foreground">Total Spent:</span>
                    <span className="font-bold text-primary">$520</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-muted-foreground">Favorite Sport:</span>
                    <span className="font-bold">Football</span>
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Recent Bookings */}
            <Card>
              <CardHeader>
                <CardTitle>Recent Bookings</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {bookingHistory.slice(0, 3).map((booking) => (
                    <div key={booking.id} className="border rounded-lg p-3">
                      <div className="flex justify-between items-start mb-2">
                        <div>
                          <p className="font-medium text-sm">{booking.name}</p>
                          <p className="text-xs text-muted-foreground">{booking.type}</p>
                        </div>
                        <Badge 
                          variant={booking.status === 'Completed' ? 'secondary' : 'default'}
                          className="text-xs"
                        >
                          {booking.status}
                        </Badge>
                      </div>
                      <div className="flex justify-between text-xs text-muted-foreground">
                        <span>{booking.date} • {booking.time}</span>
                        <span className="font-medium text-primary">${booking.amount}</span>
                      </div>
                    </div>
                  ))}
                </div>
                <Button variant="outline" className="w-full mt-4">
                  View All Bookings
                </Button>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Profile;