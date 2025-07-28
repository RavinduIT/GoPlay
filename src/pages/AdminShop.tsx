import { useState } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Plus, Edit, Trash2, Package, DollarSign, TrendingUp, Users } from 'lucide-react';
import basketballEquipment from '@/assets/basketball-equipment.jpg';
import tennisEquipment from '@/assets/tennis-equipment.jpg';
import footballEquipment from '@/assets/football-equipment.jpg';
import shopBackground from '@/assets/shop-background.jpg';

const AdminShop = () => {
  const [products, setProducts] = useState([
    {
      id: 1,
      name: 'Professional Basketball',
      category: 'Basketball',
      brand: 'Nike',
      price: 89.99,
      stock: 25,
      image: basketballEquipment,
      status: 'active'
    },
    {
      id: 2,
      name: 'Tennis Racket Pro',
      category: 'Tennis',
      brand: 'Wilson',
      price: 149.99,
      stock: 15,
      image: tennisEquipment,
      status: 'active'
    },
    {
      id: 3,
      name: 'Soccer Ball Official',
      category: 'Football',
      brand: 'Adidas',
      price: 45.99,
      stock: 30,
      image: footballEquipment,
      status: 'active'
    }
  ]);

  const [newProduct, setNewProduct] = useState({
    name: '',
    category: '',
    brand: '',
    price: '',
    stock: '',
    description: ''
  });

  const handleAddProduct = () => {
    if (newProduct.name && newProduct.price) {
      const product = {
        id: Date.now(),
        ...newProduct,
        price: parseFloat(newProduct.price),
        stock: parseInt(newProduct.stock),
        image: basketballEquipment,
        status: 'active'
      };
      setProducts([...products, product]);
      setNewProduct({ name: '', category: '', brand: '', price: '', stock: '', description: '' });
    }
  };

  const stats = [
    { title: 'Total Products', value: products.length, icon: Package, color: 'text-blue-600' },
    { title: 'Total Revenue', value: '$12,456', icon: DollarSign, color: 'text-green-600' },
    { title: 'Orders Today', value: '23', icon: TrendingUp, color: 'text-purple-600' },
    { title: 'Active Customers', value: '1,234', icon: Users, color: 'text-orange-600' }
  ];

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <div 
        className="relative h-64 bg-cover bg-center flex items-center justify-center"
        style={{ backgroundImage: `url(${shopBackground})` }}
      >
        <div className="absolute inset-0 bg-black/60"></div>
        <div className="relative text-center text-white">
          <h1 className="text-4xl font-bold mb-2">Shop Admin Dashboard</h1>
          <p className="text-xl opacity-90">Manage your sports equipment inventory</p>
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

        <Tabs defaultValue="products" className="space-y-6">
          <TabsList>
            <TabsTrigger value="products">Products</TabsTrigger>
            <TabsTrigger value="orders">Orders</TabsTrigger>
            <TabsTrigger value="analytics">Analytics</TabsTrigger>
          </TabsList>

          <TabsContent value="products" className="space-y-6">
            {/* Add Product */}
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center gap-2">
                  <Plus className="h-5 w-5" />
                  Add New Product
                </CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid md:grid-cols-3 gap-4 mb-4">
                  <div>
                    <Label htmlFor="name">Product Name</Label>
                    <Input
                      id="name"
                      value={newProduct.name}
                      onChange={(e) => setNewProduct({...newProduct, name: e.target.value})}
                      placeholder="Enter product name"
                    />
                  </div>
                  <div>
                    <Label htmlFor="category">Category</Label>
                    <Select value={newProduct.category} onValueChange={(value) => setNewProduct({...newProduct, category: value})}>
                      <SelectTrigger>
                        <SelectValue placeholder="Select category" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem value="Basketball">Basketball</SelectItem>
                        <SelectItem value="Tennis">Tennis</SelectItem>
                        <SelectItem value="Football">Football</SelectItem>
                        <SelectItem value="Volleyball">Volleyball</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <div>
                    <Label htmlFor="brand">Brand</Label>
                    <Input
                      id="brand"
                      value={newProduct.brand}
                      onChange={(e) => setNewProduct({...newProduct, brand: e.target.value})}
                      placeholder="Enter brand"
                    />
                  </div>
                  <div>
                    <Label htmlFor="price">Price ($)</Label>
                    <Input
                      id="price"
                      type="number"
                      value={newProduct.price}
                      onChange={(e) => setNewProduct({...newProduct, price: e.target.value})}
                      placeholder="0.00"
                    />
                  </div>
                  <div>
                    <Label htmlFor="stock">Stock</Label>
                    <Input
                      id="stock"
                      type="number"
                      value={newProduct.stock}
                      onChange={(e) => setNewProduct({...newProduct, stock: e.target.value})}
                      placeholder="0"
                    />
                  </div>
                  <div className="flex items-end">
                    <Button onClick={handleAddProduct} className="w-full">
                      Add Product
                    </Button>
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Products List */}
            <Card>
              <CardHeader>
                <CardTitle>Product Inventory</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {products.map((product) => (
                    <div key={product.id} className="flex items-center gap-4 p-4 border rounded-lg">
                      <img 
                        src={product.image} 
                        alt={product.name}
                        className="w-16 h-16 object-cover rounded"
                      />
                      <div className="flex-1">
                        <h3 className="font-semibold">{product.name}</h3>
                        <p className="text-sm text-muted-foreground">{product.brand} • {product.category}</p>
                        <div className="flex items-center gap-2 mt-1">
                          <Badge variant="outline">${product.price}</Badge>
                          <Badge variant={product.stock > 10 ? 'default' : 'destructive'}>
                            Stock: {product.stock}
                          </Badge>
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

          <TabsContent value="orders">
            <Card>
              <CardHeader>
                <CardTitle>Recent Orders</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  <div className="flex items-center justify-between p-4 border rounded-lg">
                    <div>
                      <p className="font-semibold">Order #1234</p>
                      <p className="text-sm text-muted-foreground">John Doe • 2 items</p>
                    </div>
                    <div className="text-right">
                      <p className="font-semibold">$189.98</p>
                      <Badge>Processing</Badge>
                    </div>
                  </div>
                  <div className="flex items-center justify-between p-4 border rounded-lg">
                    <div>
                      <p className="font-semibold">Order #1235</p>
                      <p className="text-sm text-muted-foreground">Jane Smith • 1 item</p>
                    </div>
                    <div className="text-right">
                      <p className="font-semibold">$89.99</p>
                      <Badge variant="secondary">Delivered</Badge>
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="analytics">
            <Card>
              <CardHeader>
                <CardTitle>Sales Analytics</CardTitle>
              </CardHeader>
              <CardContent>
                <div className="grid md:grid-cols-2 gap-6">
                  <div>
                    <h3 className="font-semibold mb-4">Top Selling Categories</h3>
                    <div className="space-y-2">
                      <div className="flex justify-between">
                        <span>Basketball</span>
                        <span>45%</span>
                      </div>
                      <div className="flex justify-between">
                        <span>Tennis</span>
                        <span>30%</span>
                      </div>
                      <div className="flex justify-between">
                        <span>Football</span>
                        <span>25%</span>
                      </div>
                    </div>
                  </div>
                  <div>
                    <h3 className="font-semibold mb-4">Monthly Revenue</h3>
                    <p className="text-2xl font-bold text-green-600">$24,890</p>
                    <p className="text-sm text-muted-foreground">+12% from last month</p>
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

export default AdminShop;