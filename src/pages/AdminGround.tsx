import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Plus, Edit, Trash2, MapPin, Calendar, Clock, DollarSign, TrendingUp, Users } from 'lucide-react';
import bookingBackground from '@/assets/booking-background.jpg';
import sportsComplexImage from '@/assets/sports-complex.jpg';
import tennisCourtImage from '@/assets/tennis-court.jpg';
import basketballCourtImage from '@/assets/basketball-court.jpg';

const AdminGround = () => {
  const [grounds, setGrounds] = useState([
    {
      id: 1,
      name: 'City Sports Complex',
      type: 'Multi-Sport',
      sports: ['Football', 'Basketball', 'Tennis'],
      location: 'Downtown Area',
      price: 25,
      image: sportsComplexImage,
      status: 'active',
      amenities: ['Parking', 'Changing Rooms', 'Equipment Rental']
    },
    {
      id: 2,
      name: 'Tennis Center Pro',
      type: 'Tennis Courts',
      sports: ['Tennis'],
      location: 'Sports District',
      price: 30,
      image: tennisCourtImage,
      status: 'active',
      amenities: ['Parking', 'Pro Shop', 'Coaching']
    },
    {
      id: 3,
      name: 'Basketball Arena',
      type: 'Basketball',
      sports: ['Basketball'],
      location: 'North Side',
      price: 20,
      image: basketballCourtImage,
      status: 'active',
      amenities: ['Parking', 'Sound System', 'Scoreboard']
    }
  ]);

  const [newGround, setNewGround] = useState({
    name: '',
    type: '',
    location: '',
    price: '',
    description: ''
  });

  const handleAddGround = () => {
    if (newGround.name && newGround.price) {
      const ground = {
        id: Date.now(),
        ...newGround,
        price: parseFloat(newGround.price),
        sports: [],
        image: sportsComplexImage,
        status: 'active',
        amenities: []
      };
      setGrounds([...grounds, ground]);
      setNewGround({ name: '', type: '', location: '', price: '', description: '' });
    }
  };

  const stats = [
    { title: 'Total Grounds', value: grounds.length, icon: MapPin, color: 'text-blue-600' },
    { title: 'Today Revenue', value: '$1,250', icon: DollarSign, color: 'text-green-600' },
    { title: 'Bookings Today', value: '15', icon: Calendar, color: 'text-purple-600' },
    { title: 'Active Users', value: '234', icon: Users, color: 'text-orange-600' }
  ];

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <div 
        className="relative h-64 bg-cover bg-center flex items-center justify-center"
        style={{ backgroundImage: `url(${bookingBackground})` }}
      >
        <div className="absolute inset-0 bg-black/60"></div>
        <div className="relative text-center text-white">
          <h1 className="text-4xl font-bold mb-2">Ground Admin Dashboard</h1>
          <p className="text-xl opacity-90">Manage your sports facilities and bookings</p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-8">
        {/* Stats */}
        <div className="grid md:grid-cols-4 gap-6 mb-8">
          {stats.map((stat, index) => (
            <Card key={index}>
              <CardContent className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="text-sm text-muted-foreground">{stat.title}</p>
                    <p className="text-2xl font-bold">{stat.value}</p>
                  </div>
                  <stat.icon className={`h-8 w-8 ${stat.color}`} />
                </div>
              </CardContent>
            </Card>
          ))}
        </div>

        <Tabs defaultValue="grounds" className="space-y-6">
          <TabsList>
            <TabsTrigger value="grounds">Grounds</TabsTrigger>
            <TabsTrigger value="bookings">Bookings</TabsTrigger>
            <TabsTrigger value="schedule">Schedule</TabsTrigger>
            <TabsTrigger value="analytics">Analytics</TabsTrigger>
          </TabsList>

          <TabsContent value="grounds" className="space-y-6">
            {/* Add Ground */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Plus className="h-5 w-5" />
                  Add New Ground
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid md:grid-cols-2 gap-4 mb-4">
                  <div>
                    <Label htmlFor="groundName">Ground Name</Label>
                    <Input
                      id="groundName"
                      value={newGround.name}
                      onChange={(e) => setNewGround({...newGround, name: e.target.value})}
                      placeholder="Enter ground name"
                    />
                  </div>
                  <div>
                    <Label htmlFor="groundType">Ground Type</Label>
                    <Select value={newGround.type} onValueChange={(value) => setNewGround({...newGround, type: value})}>
                      <SelectTrigger>
                        <SelectValue placeholder="Select type" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="Multi-Sport">Multi-Sport Facility</SelectItem>
                        <SelectItem value="Tennis">Tennis Courts</SelectItem>
                        <SelectItem value="Basketball">Basketball Courts</SelectItem>
                        <SelectItem value="Football">Football Field</SelectItem>
                        <SelectItem value="Volleyball">Volleyball Court</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <div>
                    <Label htmlFor="location">Location</Label>
                    <Input
                      id="location"
                      value={newGround.location}
                      onChange={(e) => setNewGround({...newGround, location: e.target.value})}
                      placeholder="Enter location"
                    />
                  </div>
                  <div>
                    <Label htmlFor="groundPrice">Price per Hour ($)</Label>
                    <Input
                      id="groundPrice"
                      type="number"
                      value={newGround.price}
                      onChange={(e) => setNewGround({...newGround, price: e.target.value})}
                      placeholder="0.00"
                    />
                  </div>
                  <div className="md:col-span-2">
                    <Label htmlFor="description">Description</Label>
                    <Textarea
                      id="description"
                      value={newGround.description}
                      onChange={(e) => setNewGround({...newGround, description: e.target.value})}
                      placeholder="Enter ground description and amenities"
                    />
                  </div>
                  <div className="md:col-span-2">
                    <Button onClick={handleAddGround} className="w-full">
                      Add Ground
                    </Button>
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Grounds List */}
            <Card>
              <CardHeader>
                <CardTitle>Your Grounds</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {grounds.map((ground) => (
                    <div key={ground.id} className="flex items-center gap-4 p-4 border rounded-lg">
                      <img 
                        src={ground.image} 
                        alt={ground.name}
                        className="w-20 h-20 object-cover rounded"
                      />
                      <div className="flex-1">
                        <h3 className="font-semibold">{ground.name}</h3>
                        <p className="text-sm text-muted-foreground">{ground.type} • {ground.location}</p>
                        <div className="flex items-center gap-2 mt-1">
                          <Badge variant="outline">${ground.price}/hour</Badge>
                          <Badge variant={ground.status === 'active' ? 'default' : 'secondary'}>
                            {ground.status}
                          </Badge>
                        </div>
                        <div className="flex flex-wrap gap-1 mt-2">
                          {ground.sports.map((sport) => (
                            <Badge key={sport} variant="secondary" className="text-xs">
                              {sport}
                            </Badge>
                          ))}
                        </div>
                      </div>
                      <div className="flex gap-2">
                        <Button variant="outline" size="sm">
                          <Edit className="h-4 w-4" />
                        </Button>
                        <Button variant="outline" size="sm" className="text-red-600 hover:text-red-700">
                          <Trash2 className="h-4 w-4" />
                        </Button>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="bookings">
            <Card>
              <CardHeader>
                <CardTitle>Recent Bookings</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div className="flex items-center justify-between p-4 border rounded-lg">
                    <div>
                      <p className="font-semibold">City Sports Complex</p>
                      <p className="text-sm text-muted-foreground">John Doe • 2 hours • 14:00-16:00</p>
                    </div>
                    <div className="text-right">
                      <p className="font-semibold">$50.00</p>
                      <Badge>Confirmed</Badge>
                    </div>
                  </div>
                  <div className="flex items-center justify-between p-4 border rounded-lg">
                    <div>
                      <p className="font-semibold">Tennis Center Pro</p>
                      <p className="text-sm text-muted-foreground">Jane Smith • 1 hour • 09:00-10:00</p>
                    </div>
                    <div className="text-right">
                      <p className="font-semibold">$30.00</p>
                      <Badge variant="secondary">Completed</Badge>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="schedule">
            <Card>
              <CardHeader>
                <CardTitle>Today's Schedule</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div className="grid grid-cols-8 gap-2 text-sm">
                    <div className="font-semibold">Time</div>
                    <div className="font-semibold col-span-7">Bookings</div>
                  </div>
                  {['08:00', '09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'].map((time) => (
                    <div key={time} className="grid grid-cols-8 gap-2 py-2 border-b">
                      <div className="font-medium">{time}</div>
                      <div className="col-span-7">
                        <Badge variant="outline" className="mr-2">City Complex - Court 1</Badge>
                        <Badge variant="secondary">Tennis Center - Court A</Badge>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="analytics">
            <Card>
              <CardHeader>
                <CardTitle>Analytics Overview</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid md:grid-cols-2 gap-6">
                  <div>
                    <h3 className="font-semibold mb-4">Most Popular Grounds</h3>
                    <div className="space-y-2">
                      <div className="flex justify-between">
                        <span>City Sports Complex</span>
                        <span>65%</span>
                      </div>
                      <div className="flex justify-between">
                        <span>Tennis Center Pro</span>
                        <span>25%</span>
                      </div>
                      <div className="flex justify-between">
                        <span>Basketball Arena</span>
                        <span>10%</span>
                      </div>
                    </div>
                  </div>
                  <div>
                    <h3 className="font-semibold mb-4">Monthly Revenue</h3>
                    <p className="text-2xl font-bold text-green-600">$8,450</p>
                    <p className="text-sm text-muted-foreground">+18% from last month</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
};

export default AdminGround;