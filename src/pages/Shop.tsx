import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Heart, Eye, Search, ShoppingCart, Star, Filter } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import shopBackground from '@/assets/shop-background.jpg';
import basketballEquipment from '@/assets/basketball-equipment.jpg';
import tennisEquipment from '@/assets/tennis-equipment.jpg';
import footballEquipment from '@/assets/football-equipment.jpg';

const Shop = () => {
  const navigate = useNavigate();
  const [selectedCategory, setSelectedCategory] = useState('');
  const [selectedBrand, setSelectedBrand] = useState('');
  const [priceRange, setPriceRange] = useState('');
  const [searchQuery, setSearchQuery] = useState('');
  const [cartItems, setCartItems] = useState<number[]>([]);

  const categories = ['Football', 'Basketball', 'Tennis', 'Running', 'Swimming', 'Fitness'];
  const brands = ['Nike', 'Adidas', 'Wilson', 'Spalding', 'Under Armour', 'Puma'];
  const priceRanges = ['$0-25', '$25-50', '$50-100', '$100+'];

  const products = [
    {
      id: 1,
      name: 'Professional Football',
      category: 'Football',
      brand: 'Nike',
      price: 45.99,
      originalPrice: 59.99,
      rating: 4.8,
      reviews: 234,
      image: footballEquipment,
      inStock: true,
      features: ['Official Size 5', 'FIFA Approved', 'Durable Construction'],
      description: 'Professional quality football for competitive matches'
    },
    {
      id: 2,
      name: 'Basketball Pro',
      category: 'Basketball',
      brand: 'Spalding',
      price: 89.99,
      originalPrice: 109.99,
      rating: 4.9,
      reviews: 156,
      image: basketballEquipment,
      inStock: true,
      features: ['Official NBA Size', 'Composite Leather', 'Superior Grip'],
      description: 'Premium basketball used in professional leagues'
    },
    {
      id: 3,
      name: 'Tennis Racket Elite',
      category: 'Tennis',
      brand: 'Wilson',
      price: 199.99,
      originalPrice: 249.99,
      rating: 4.7,
      reviews: 89,
      image: '🎾',
      inStock: true,
      features: ['Carbon Fiber Frame', 'Pro String Tension', 'Ergonomic Handle'],
      description: 'Professional tennis racket for advanced players'
    },
    {
      id: 4,
      name: 'Running Shoes Max',
      category: 'Running',
      brand: 'Nike',
      price: 129.99,
      originalPrice: 159.99,
      rating: 4.6,
      reviews: 312,
      image: '👟',
      inStock: true,
      features: ['Air Cushioning', 'Breathable Mesh', 'Lightweight'],
      description: 'High-performance running shoes for all terrains'
    },
    {
      id: 5,
      name: 'Swimming Goggles Pro',
      category: 'Swimming',
      brand: 'Speedo',
      price: 34.99,
      originalPrice: 44.99,
      rating: 4.5,
      reviews: 167,
      image: '🥽',
      inStock: false,
      features: ['Anti-Fog Coating', 'UV Protection', 'Adjustable Strap'],
      description: 'Professional swimming goggles for competitive swimming'
    },
    {
      id: 6,
      name: 'Fitness Dumbbells Set',
      category: 'Fitness',
      brand: 'Under Armour',
      price: 149.99,
      originalPrice: 189.99,
      rating: 4.7,
      reviews: 203,
      image: '🏋️',
      inStock: true,
      features: ['Adjustable Weight', 'Non-Slip Grip', 'Space Saving Design'],
      description: 'Complete dumbbell set for home gym workouts'
    }
  ];

  const filteredProducts = products.filter(product => {
    // Search filter
    if (searchQuery && !product.name.toLowerCase().includes(searchQuery.toLowerCase()) && 
        !product.brand.toLowerCase().includes(searchQuery.toLowerCase()) &&
        !product.category.toLowerCase().includes(searchQuery.toLowerCase())) return false;
    
    // Category filter  
    if (selectedCategory && selectedCategory !== 'all-categories' && product.category !== selectedCategory) return false;
    
    // Brand filter
    if (selectedBrand && selectedBrand !== 'all-brands' && product.brand !== selectedBrand) return false;
    
    // Price range filter
    if (priceRange && priceRange !== 'any-price') {
      const price = product.price;
      switch (priceRange) {
        case '$0-25':
          if (price > 25) return false;
          break;
        case '$25-50':
          if (price < 25 || price > 50) return false;
          break;
        case '$50-100':
          if (price < 50 || price > 100) return false;
          break;
        case '$100+':
          if (price < 100) return false;
          break;
      }
    }
    
    return true;
  });

  const addToCart = (productId: number) => {
    setCartItems(prev => [...prev, productId]);
  };

  const isInCart = (productId: number) => {
    return cartItems.includes(productId);
  };

  return (
    <div className="min-h-screen bg-background">
      {/* Header Section */}
      <div className="bg-gradient-hero py-16 relative overflow-hidden">
        <div 
          className="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20"
          style={{ backgroundImage: `url(${shopBackground})` }}
        />
        <div className="container mx-auto px-4 text-center relative z-10">
          <h1 className="text-4xl md:text-5xl font-bold text-primary-foreground mb-4">
            Sports Equipment Shop
          </h1>
          <p className="text-xl text-primary-foreground/90 max-w-2xl mx-auto">
            Quality sports gear from top brands for all your athletic needs
          </p>
        </div>
      </div>

      {/* Search & Filter Section */}
      <div className="container mx-auto px-4 -mt-8 relative z-10">
        <Card className="shadow-strong bg-card/95 backdrop-blur-sm">
          <CardContent className="p-6">
            <div className="grid md:grid-cols-4 gap-4">
              <div>
                <div className="flex gap-2">
                  <Input
                    placeholder="Search products..."
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    className="flex-1"
                  />
                  <Button className="bg-gradient-primary">
                    <Search className="h-4 w-4" />
                  </Button>
                </div>
              </div>
              <div>
                <Select value={selectedCategory} onValueChange={setSelectedCategory}>
                  <SelectTrigger>
                    <SelectValue placeholder="All categories" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all-categories">All Categories</SelectItem>
                    {categories.map((category) => (
                      <SelectItem key={category} value={category}>
                        {category}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              <div>
                <Select value={selectedBrand} onValueChange={setSelectedBrand}>
                  <SelectTrigger>
                    <SelectValue placeholder="All brands" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all-brands">All Brands</SelectItem>
                    {brands.map((brand) => (
                      <SelectItem key={brand} value={brand}>
                        {brand}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
              <div>
                <Select value={priceRange} onValueChange={setPriceRange}>
                  <SelectTrigger>
                    <SelectValue placeholder="Price range" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="any-price">Any Price</SelectItem>
                    {priceRanges.map((range) => (
                      <SelectItem key={range} value={range}>
                        {range}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Products Section */}
      <div className="container mx-auto px-4 py-12">
        <div className="flex items-center justify-between mb-8">
          <h2 className="text-2xl font-bold text-foreground">
            Products ({filteredProducts.length})
          </h2>
          <div className="flex gap-2">
            <Button variant="outline" size="sm">
              <Filter className="mr-2 h-4 w-4" />
              Filters
            </Button>
            <Select defaultValue="featured">
              <SelectTrigger className="w-[180px]">
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="featured">Featured</SelectItem>
                <SelectItem value="price-low">Price: Low to High</SelectItem>
                <SelectItem value="price-high">Price: High to Low</SelectItem>
                <SelectItem value="rating">Highest Rated</SelectItem>
              </SelectContent>
            </Select>
          </div>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          {filteredProducts.map((product) => (
            <Card key={product.id} className="group hover:shadow-medium transition-all duration-300 hover:-translate-y-1 relative">
              {!product.inStock && (
                <div className="absolute top-4 left-4 z-10">
                  <Badge variant="destructive">Out of Stock</Badge>
                </div>
              )}
              {product.originalPrice > product.price && (
                <div className="absolute top-4 right-4 z-10">
                  <Badge className="bg-red-500 text-white">
                    Sale
                  </Badge>
                </div>
              )}
              
              <CardHeader className="pb-4">
                <div className="text-center mb-4">
                  <img 
                    src={typeof product.image === 'string' && product.image.startsWith('http') ? product.image : basketballEquipment} 
                    alt={product.name}
                    className="w-20 h-20 object-cover rounded mx-auto mb-2"
                  />
                  <CardTitle className="text-lg text-card-foreground mb-1">
                    {product.name}
                  </CardTitle>
                  <p className="text-sm text-muted-foreground">{product.brand}</p>
                </div>
                
                <div className="flex items-center justify-between mb-2">
                  <div className="flex items-center gap-1">
                    <Star className="h-4 w-4 fill-yellow-400 text-yellow-400" />
                    <span className="font-medium text-sm">{product.rating}</span>
                    <span className="text-xs text-muted-foreground">({product.reviews})</span>
                  </div>
                  <Badge variant="secondary" className="text-xs">
                    {product.category}
                  </Badge>
                </div>

                <div className="text-center">
                  <div className="flex items-center justify-center gap-2">
                    <span className="text-2xl font-bold text-primary">
                      ${product.price}
                    </span>
                    {product.originalPrice > product.price && (
                      <span className="text-sm text-muted-foreground line-through">
                        ${product.originalPrice}
                      </span>
                    )}
                  </div>
                </div>
              </CardHeader>
              
              <CardContent>
                <p className="text-sm text-muted-foreground mb-4">
                  {product.description}
                </p>

                <div className="mb-4">
                  <h4 className="font-medium text-card-foreground mb-2 text-sm">Features:</h4>
                  <div className="flex flex-wrap gap-1">
                    {product.features.map((feature) => (
                      <Badge key={feature} variant="outline" className="text-xs">
                        {feature}
                      </Badge>
                    ))}
                  </div>
                </div>

                <div className="flex gap-2">
                  <Button
                    variant="outline"
                    size="icon"
                    className="flex-shrink-0"
                  >
                    <Heart className="h-4 w-4" />
                  </Button>
                  <Button
                    variant="outline"
                    className="flex-1"
                    onClick={() => navigate(`/product-details?id=${product.id}`)}
                  >
                    View Details
                  </Button>
                  <Button
                    className="flex-1 bg-gradient-primary"
                    onClick={() => navigate(`/product-details?id=${product.id}`)}
                  >
                    <ShoppingCart className="mr-2 h-4 w-4" />
                    Buy Now
                  </Button>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>

      {/* Cart Summary */}
      {cartItems.length > 0 && (
        <div className="fixed bottom-6 right-6 z-50">
          <Card className="shadow-strong bg-card/95 backdrop-blur-sm">
            <CardContent className="p-4">
              <div className="flex items-center gap-2 mb-2">
                <ShoppingCart className="h-5 w-5 text-primary" />
                <span className="font-medium">{cartItems.length} items in cart</span>
              </div>
              <Button 
                size="sm" 
                className="w-full bg-gradient-primary"
                onClick={() => navigate('/cart')}
              >
                View Cart
              </Button>
            </CardContent>
          </Card>
        </div>
      )}
    </div>
  );
};

export default Shop;