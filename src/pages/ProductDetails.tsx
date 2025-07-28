import { useState } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { Star, ShoppingCart, Heart, Share2, ArrowLeft, Plus, Minus, MessageSquare } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Separator } from '@/components/ui/separator';
import { useToast } from '@/hooks/use-toast';
import basketballEquipment from '@/assets/basketball-equipment.jpg';
import footballEquipment from '@/assets/football-equipment.jpg';
import tennisEquipment from '@/assets/tennis-equipment.jpg';

const ProductDetails = () => {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const productId = searchParams.get('id');
  const { toast } = useToast();
  
  const [selectedColor, setSelectedColor] = useState('');
  const [selectedSize, setSelectedSize] = useState('');
  const [quantity, setQuantity] = useState(1);
  
  // Review state
  const [reviewText, setReviewText] = useState('');
  const [reviewRating, setReviewRating] = useState(0);

  // Mock product data (in real app, fetch based on productId)
  const product = {
    id: productId || '1',
    name: 'Professional Basketball',
    brand: 'Nike',
    price: 89,
    originalPrice: 120,
    rating: 4.8,
    reviews: 156,
    category: 'Basketball',
    description: 'High-quality professional basketball made with premium leather for excellent grip and durability. Perfect for indoor and outdoor courts.',
    image: basketballEquipment,
    features: ['Premium Leather', 'Official Size', 'Deep Channel Design', 'Moisture Wicking'],
    colors: ['Orange', 'Brown', 'White'],
    sizes: ['Size 6', 'Size 7'],
    inStock: true,
    stockCount: 25,
    images: [basketballEquipment, footballEquipment, tennisEquipment],
    specifications: {
      'Material': 'Premium Leather',
      'Size': 'Official (29.5-29.875 inches)',
      'Weight': '22 oz',
      'Brand': 'Nike',
      'Model': 'Elite Championship'
    }
  };

  const reviews = [
    {
      id: 1,
      user: 'John Smith',
      avatar: '',
      rating: 5,
      date: '2024-01-15',
      comment: 'Excellent quality basketball! Great grip and feels professional.'
    },
    {
      id: 2,
      user: 'Sarah Johnson',
      avatar: '',
      rating: 4,
      date: '2024-01-10',
      comment: 'Good quality but slightly expensive. Overall satisfied with the purchase.'
    },
    {
      id: 3,
      user: 'Mike Chen',
      avatar: '',
      rating: 5,
      date: '2024-01-05',
      comment: 'Perfect for competitive play. Highly recommend!'
    }
  ];

  const handleAddToCart = () => {
    if (!selectedColor || !selectedSize) {
      toast({
        title: "Selection Required",
        description: "Please select color and size before adding to cart.",
        variant: "destructive"
      });
      return;
    }
    
    toast({
      title: "Added to Cart",
      description: `${quantity}x ${product.name} added to your cart.`
    });
  };

  const handleBuyNow = () => {
    if (!selectedColor || !selectedSize) {
      toast({
        title: "Selection Required", 
        description: "Please select color and size before proceeding.",
        variant: "destructive"
      });
      return;
    }
    
    // Navigate to payment page with product details
    const params = new URLSearchParams({
      type: 'shop',
      productId: product.id,
      productName: product.name,
      productPrice: product.price.toString(),
      color: selectedColor,
      size: selectedSize,
      quantity: quantity.toString()
    });
    
    navigate(`/payment?${params.toString()}`);
  };


  const handleSubmitReview = () => {
    if (!reviewText || reviewRating === 0) {
      toast({
        title: "Review Required",
        description: "Please provide a rating and comment.",
        variant: "destructive"
      });
      return;
    }

    toast({
      title: "Review Submitted",
      description: "Thank you for your feedback!"
    });
    
    setReviewText('');
    setReviewRating(0);
  };

  return (
    <div className="min-h-screen bg-background">
      <div className="container mx-auto px-4 py-8">
        {/* Back Button */}
        <Button 
          variant="outline" 
          onClick={() => navigate('/shop')}
          className="mb-6"
        >
          <ArrowLeft className="mr-2 h-4 w-4" />
          Back to Shop
        </Button>

        <div className="grid lg:grid-cols-2 gap-12">
          {/* Product Images */}
          <div>
            <div className="mb-4">
              <img 
                src={product.image} 
                alt={product.name}
                className="w-full h-96 object-cover rounded-lg"
              />
            </div>
            <div className="grid grid-cols-3 gap-2">
              {product.images.map((img, index) => (
                <img 
                  key={index}
                  src={img} 
                  alt={`${product.name} ${index + 1}`}
                  className="w-full h-24 object-cover rounded cursor-pointer hover:opacity-80"
                />
              ))}
            </div>
          </div>

          {/* Product Info */}
          <div>
            <div className="mb-6">
              <h1 className="text-3xl font-bold text-foreground mb-2">
                {product.name}
              </h1>
              <p className="text-lg text-muted-foreground">{product.brand}</p>
              
              <div className="flex items-center gap-4 mt-3">
                <div className="flex items-center gap-1">
                  <Star className="h-5 w-5 fill-yellow-400 text-yellow-400" />
                  <span className="font-medium">{product.rating}</span>
                  <span className="text-muted-foreground">({product.reviews} reviews)</span>
                </div>
                <Badge variant="secondary">{product.category}</Badge>
              </div>
            </div>

            {/* Pricing */}
            <div className="mb-6">
              <div className="flex items-center gap-3">
                <span className="text-3xl font-bold text-primary">
                  ${product.price}
                </span>
                {product.originalPrice > product.price && (
                  <span className="text-xl text-muted-foreground line-through">
                    ${product.originalPrice}
                  </span>
                )}
              </div>
              <p className="text-sm text-green-600 mt-1">
                {product.inStock ? `In Stock (${product.stockCount} available)` : 'Out of Stock'}
              </p>
            </div>

            {/* Color Selection */}
            <div className="mb-4">
              <Label className="text-base font-medium mb-3 block">Color</Label>
              <div className="flex gap-2">
                {product.colors.map((color) => (
                  <Button
                    key={color}
                    variant={selectedColor === color ? "default" : "outline"}
                    size="sm"
                    onClick={() => setSelectedColor(color)}
                  >
                    {color}
                  </Button>
                ))}
              </div>
            </div>

            {/* Size Selection */}
            <div className="mb-4">
              <Label className="text-base font-medium mb-3 block">Size</Label>
              <Select value={selectedSize} onValueChange={setSelectedSize}>
                <SelectTrigger className="w-40">
                  <SelectValue placeholder="Select size" />
                </SelectTrigger>
                <SelectContent>
                  {product.sizes.map((size) => (
                    <SelectItem key={size} value={size}>
                      {size}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            {/* Quantity */}
            <div className="mb-6">
              <Label className="text-base font-medium mb-3 block">Quantity</Label>
              <div className="flex items-center gap-3">
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => setQuantity(Math.max(1, quantity - 1))}
                  disabled={quantity <= 1}
                >
                  <Minus className="h-4 w-4" />
                </Button>
                <span className="text-lg font-medium w-12 text-center">{quantity}</span>
                <Button
                  variant="outline"
                  size="sm"
                  onClick={() => setQuantity(quantity + 1)}
                  disabled={quantity >= product.stockCount}
                >
                  <Plus className="h-4 w-4" />
                </Button>
              </div>
            </div>

            {/* Action Buttons */}
            <div className="flex gap-4 mb-8">
              <Button 
                onClick={handleAddToCart}
                variant="outline" 
                className="flex-1"
                disabled={!product.inStock}
              >
                <ShoppingCart className="mr-2 h-4 w-4" />
                Add to Cart
              </Button>
              <Button 
                onClick={handleBuyNow}
                className="flex-1 bg-gradient-primary"
                disabled={!product.inStock}
              >
                Buy Now
              </Button>
            </div>

            {/* Product Description */}
            <div className="mb-6">
              <h3 className="text-lg font-semibold mb-3">Description</h3>
              <p className="text-muted-foreground">{product.description}</p>
            </div>

            {/* Features */}
            <div className="mb-6">
              <h3 className="text-lg font-semibold mb-3">Features</h3>
              <div className="flex flex-wrap gap-2">
                {product.features.map((feature) => (
                  <Badge key={feature} variant="outline">
                    {feature}
                  </Badge>
                ))}
              </div>
            </div>
          </div>
        </div>


        {/* Reviews Section */}
        <div className="mt-12">
          <h2 className="text-2xl font-bold mb-6">Customer Reviews</h2>
          
          {/* Add Review Form */}
          <Card className="mb-6">
            <CardHeader>
              <CardTitle>Write a Review</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="mb-4">
                <Label>Rating</Label>
                <div className="flex gap-1 mt-2">
                  {[1, 2, 3, 4, 5].map((star) => (
                    <button
                      key={star}
                      onClick={() => setReviewRating(star)}
                      className="p-1"
                    >
                      <Star 
                        className={`h-6 w-6 ${
                          star <= reviewRating 
                            ? 'fill-yellow-400 text-yellow-400' 
                            : 'text-gray-300'
                        }`}
                      />
                    </button>
                  ))}
                </div>
              </div>
              <div className="mb-4">
                <Label htmlFor="review">Your Review</Label>
                <Textarea
                  id="review"
                  value={reviewText}
                  onChange={(e) => setReviewText(e.target.value)}
                  placeholder="Share your experience with this product..."
                  className="mt-2"
                />
              </div>
              <Button onClick={handleSubmitReview} className="bg-gradient-primary">
                <MessageSquare className="mr-2 h-4 w-4" />
                Submit Review
              </Button>
            </CardContent>
          </Card>

          {/* Reviews List */}
          <div className="space-y-4">
            {reviews.map((review) => (
              <Card key={review.id}>
                <CardContent className="pt-6">
                  <div className="flex items-start gap-4">
                    <Avatar>
                      <AvatarImage src={review.avatar} />
                      <AvatarFallback>{review.user.charAt(0)}</AvatarFallback>
                    </Avatar>
                    <div className="flex-1">
                      <div className="flex items-center gap-3 mb-2">
                        <h4 className="font-medium">{review.user}</h4>
                        <div className="flex items-center gap-1">
                          {[1, 2, 3, 4, 5].map((star) => (
                            <Star 
                              key={star}
                              className={`h-4 w-4 ${
                                star <= review.rating 
                                  ? 'fill-yellow-400 text-yellow-400' 
                                  : 'text-gray-300'
                              }`}
                            />
                          ))}
                        </div>
                        <span className="text-sm text-muted-foreground">
                          {review.date}
                        </span>
                      </div>
                      <p className="text-muted-foreground">{review.comment}</p>
                    </div>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductDetails;