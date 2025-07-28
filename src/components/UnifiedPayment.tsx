import { useState, useEffect } from 'react';
import { useNavigate, useSearchParams } from 'react-router-dom';
import { ArrowLeft, CreditCard, Shield, CheckCircle, Clock, MapPin, User, Calendar, ShoppingBag } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Separator } from '@/components/ui/separator';
import { Badge } from '@/components/ui/badge';
import { useToast } from '@/hooks/use-toast';
import basketballEquipment from '@/assets/basketball-equipment.jpg';
import heroBackground from '@/assets/hero-background.jpg';

interface PaymentItem {
  id: string;
  name: string;
  price: number;
  image?: string;
  quantity?: number;
  color?: string;
  size?: string;
  date?: string;
  time?: string;
  location?: string;
  amenities?: string[];
  duration?: string;
}

interface ContactDetails {
  fullName: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  state: string;
  zipCode: string;
  country: string;
}

const UnifiedPayment = () => {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const { toast } = useToast();
  
  const paymentType = searchParams.get('type') || 'shop';
  const [currentStep, setCurrentStep] = useState(paymentType === 'shop' ? 1 : 2);
  const [paymentMethod, setPaymentMethod] = useState('card');
  
  const [contactDetails, setContactDetails] = useState<ContactDetails>({
    fullName: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    zipCode: '',
    country: ''
  });

  const [cardDetails, setCardDetails] = useState({
    number: '',
    expiry: '',
    cvv: '',
    name: ''
  });

  // Parse payment data based on type
  const getPaymentData = (): PaymentItem & { fees: any } => {
    if (paymentType === 'shop') {
      const productId = searchParams.get('productId');
      const productName = searchParams.get('productName') || 'Professional Basketball';
      const productPrice = parseFloat(searchParams.get('productPrice') || '89');
      const selectedColor = searchParams.get('color');
      const selectedSize = searchParams.get('size');
      const quantity = parseInt(searchParams.get('quantity') || '1');
      
      const subtotal = productPrice * quantity;
      const tax = subtotal * 0.08;
      const shipping = 9.99;
      
      return {
        id: productId || '1',
        name: productName,
        price: productPrice,
        image: basketballEquipment,
        quantity,
        color: selectedColor,
        size: selectedSize,
        fees: {
          subtotal,
          tax,
          shipping,
          total: subtotal + tax + shipping
        }
      };
    } else {
      // Ground/Coach booking
      const bookingId = searchParams.get('id');
      const bookingDate = searchParams.get('date');
      const bookingTime = searchParams.get('time');
      const bookingPrice = parseFloat(searchParams.get('price') || '50');
      
      const subtotal = bookingPrice;
      const serviceFee = 5.00;
      const tax = 4.40;
      
      return {
        id: bookingId || '1',
        name: paymentType === 'ground' ? 'City Sports Complex' : 'Professional Coach Session',
        price: bookingPrice,
        date: bookingDate,
        time: bookingTime,
        location: 'Downtown Area',
        duration: '2 hours',
        amenities: ['Parking', 'Changing Rooms', 'Equipment Rental'],
        fees: {
          subtotal,
          serviceFee,
          tax,
          total: subtotal + serviceFee + tax
        }
      };
    }
  };

  const paymentData = getPaymentData();

  const handleContactSubmit = () => {
    if (!contactDetails.fullName || !contactDetails.email || !contactDetails.phone || !contactDetails.address) {
      toast({
        title: "Missing Information",
        description: "Please fill in all required fields.",
        variant: "destructive"
      });
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(contactDetails.email)) {
      toast({
        title: "Invalid Email",
        description: "Please enter a valid email address.",
        variant: "destructive"
      });
      return;
    }

    setCurrentStep(2);
  };

  const handlePayment = () => {
    toast({
      title: "Payment Processing",
      description: "Redirecting to payment gateway..."
    });
    
    setTimeout(() => {
      navigate('/payment-success');
    }, 2000);
  };

  const renderOrderSummary = () => (
    <Card>
      <CardHeader>
        <CardTitle className="flex items-center gap-2">
          {paymentType === 'shop' ? <ShoppingBag className="h-5 w-5" /> : <Calendar className="h-5 w-5" />}
          {paymentType === 'shop' ? 'Order Summary' : 'Booking Summary'}
        </CardTitle>
      </CardHeader>
      <CardContent>
        <div className="space-y-4">
          {paymentType === 'shop' ? (
            <>
              <div className="flex items-center gap-4">
                <img 
                  src={paymentData.image} 
                  alt={paymentData.name}
                  className="w-16 h-16 object-cover rounded-lg"
                />
                <div className="flex-1">
                  <h3 className="font-medium">{paymentData.name}</h3>
                  {paymentData.color && paymentData.size && (
                    <p className="text-sm text-muted-foreground">
                      Color: {paymentData.color} | Size: {paymentData.size}
                    </p>
                  )}
                  <p className="text-sm text-muted-foreground">Qty: {paymentData.quantity}</p>
                </div>
                <span className="font-medium">${paymentData.price.toFixed(2)}</span>
              </div>
            </>
          ) : (
            <>
              <div>
                <h3 className="font-semibold text-lg">{paymentData.name}</h3>
                <p className="text-sm text-muted-foreground">
                  {paymentType === 'ground' ? 'Ground Booking' : 'Coach Session'}
                </p>
              </div>

              {paymentData.location && (
                <div className="flex items-center gap-2 text-sm">
                  <MapPin className="h-4 w-4 text-muted-foreground" />
                  <span>{paymentData.location}</span>
                </div>
              )}

              <div className="space-y-2">
                {paymentData.date && (
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">Date:</span>
                    <span>{paymentData.date}</span>
                  </div>
                )}
                {paymentData.time && (
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">Time:</span>
                    <span>{paymentData.time}</span>
                  </div>
                )}
                {paymentData.duration && (
                  <div className="flex justify-between text-sm">
                    <span className="text-muted-foreground">Duration:</span>
                    <span>{paymentData.duration}</span>
                  </div>
                )}
              </div>

              {paymentData.amenities && (
                <div>
                  <h4 className="font-medium mb-2">Amenities Included:</h4>
                  <div className="flex flex-wrap gap-2">
                    {paymentData.amenities.map((amenity) => (
                      <Badge key={amenity} variant="secondary" className="text-xs">
                        {amenity}
                      </Badge>
                    ))}
                  </div>
                </div>
              )}
            </>
          )}
          
          <Separator />
          
          <div className="space-y-2">
            <div className="flex justify-between">
              <span>Subtotal</span>
              <span>${paymentData.fees.subtotal.toFixed(2)}</span>
            </div>
            {paymentData.fees.tax && (
              <div className="flex justify-between">
                <span>Tax</span>
                <span>${paymentData.fees.tax.toFixed(2)}</span>
              </div>
            )}
            {paymentData.fees.shipping && (
              <div className="flex justify-between">
                <span>Shipping</span>
                <span>${paymentData.fees.shipping.toFixed(2)}</span>
              </div>
            )}
            {paymentData.fees.serviceFee && (
              <div className="flex justify-between text-sm text-muted-foreground">
                <span>Service Fee</span>
                <span>${paymentData.fees.serviceFee.toFixed(2)}</span>
              </div>
            )}
            <Separator />
            <div className="flex justify-between font-bold text-lg">
              <span>Total</span>
              <span>${paymentData.fees.total.toFixed(2)}</span>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  );

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <div 
        className="relative h-48 bg-cover bg-center flex items-center justify-center"
        style={{ backgroundImage: `url(${heroBackground})` }}
      >
        <div className="absolute inset-0 bg-black/60"></div>
        <div className="relative text-center text-white">
          <h1 className="text-4xl font-bold mb-2">Secure Payment</h1>
          <p className="text-xl opacity-90">Complete your {paymentType === 'shop' ? 'purchase' : 'booking'} with confidence</p>
        </div>
      </div>

      <div className="container mx-auto px-4 py-8">
        <div className="flex items-center gap-4 mb-8">
          <Button 
            variant="outline" 
            onClick={() => navigate(-1)}
          >
            <ArrowLeft className="mr-2 h-4 w-4" />
            Back
          </Button>
        </div>

        {/* Progress Steps for shop */}
        {paymentType === 'shop' && (
          <div className="flex items-center justify-center mb-8">
            <div className="flex items-center space-x-4">
              <div className={`flex items-center justify-center w-10 h-10 rounded-full ${
                currentStep >= 1 ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'
              }`}>
                1
              </div>
              <span className="text-sm font-medium">Contact Details</span>
              <div className="w-16 h-0.5 bg-muted"></div>
              <div className={`flex items-center justify-center w-10 h-10 rounded-full ${
                currentStep >= 2 ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'
              }`}>
                2
              </div>
              <span className="text-sm font-medium">Payment</span>
            </div>
          </div>
        )}

        <div className="grid lg:grid-cols-3 gap-8">
          {/* Order/Booking Summary */}
          <div className="lg:col-span-1">
            {renderOrderSummary()}
          </div>

          {/* Main Content */}
          <div className="lg:col-span-2">
            {currentStep === 1 && paymentType === 'shop' ? (
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <User className="h-5 w-5" />
                    Contact & Delivery Information
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="grid md:grid-cols-2 gap-6">
                    <div>
                      <Label htmlFor="fullName">Full Name *</Label>
                      <Input
                        id="fullName"
                        value={contactDetails.fullName}
                        onChange={(e) => setContactDetails({...contactDetails, fullName: e.target.value})}
                        placeholder="Enter your full name"
                      />
                    </div>
                    
                    <div>
                      <Label htmlFor="email">Email Address *</Label>
                      <Input
                        id="email"
                        type="email"
                        value={contactDetails.email}
                        onChange={(e) => setContactDetails({...contactDetails, email: e.target.value})}
                        placeholder="your.email@example.com"
                      />
                    </div>
                    
                    <div>
                      <Label htmlFor="phone">Phone Number *</Label>
                      <Input
                        id="phone"
                        value={contactDetails.phone}
                        onChange={(e) => setContactDetails({...contactDetails, phone: e.target.value})}
                        placeholder="+1 (555) 123-4567"
                      />
                    </div>
                    
                    <div>
                      <Label htmlFor="country">Country</Label>
                      <Input
                        id="country"
                        value={contactDetails.country}
                        onChange={(e) => setContactDetails({...contactDetails, country: e.target.value})}
                        placeholder="United States"
                      />
                    </div>
                    
                    <div className="md:col-span-2">
                      <Label htmlFor="address">Street Address *</Label>
                      <Textarea
                        id="address"
                        value={contactDetails.address}
                        onChange={(e) => setContactDetails({...contactDetails, address: e.target.value})}
                        placeholder="123 Main Street, Apt 4B"
                      />
                    </div>
                    
                    <div>
                      <Label htmlFor="city">City</Label>
                      <Input
                        id="city"
                        value={contactDetails.city}
                        onChange={(e) => setContactDetails({...contactDetails, city: e.target.value})}
                        placeholder="New York"
                      />
                    </div>
                    
                    <div>
                      <Label htmlFor="state">State/Province</Label>
                      <Input
                        id="state"
                        value={contactDetails.state}
                        onChange={(e) => setContactDetails({...contactDetails, state: e.target.value})}
                        placeholder="NY"
                      />
                    </div>
                    
                    <div>
                      <Label htmlFor="zipCode">ZIP/Postal Code</Label>
                      <Input
                        id="zipCode"
                        value={contactDetails.zipCode}
                        onChange={(e) => setContactDetails({...contactDetails, zipCode: e.target.value})}
                        placeholder="10001"
                      />
                    </div>
                  </div>
                  
                  <div className="mt-8">
                    <Button 
                      onClick={handleContactSubmit}
                      className="w-full bg-gradient-primary"
                      size="lg"
                    >
                      Continue to Payment
                      <ArrowLeft className="ml-2 h-4 w-4 rotate-180" />
                    </Button>
                  </div>
                </CardContent>
              </Card>
            ) : (
              <Card>
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <CreditCard className="h-5 w-5" />
                    Payment Information
                  </CardTitle>
                </CardHeader>
                <CardContent className="space-y-6">
                  {/* Contact Details Summary for shop */}
                  {paymentType === 'shop' && currentStep === 2 && (
                    <div className="bg-muted/50 p-4 rounded-lg">
                      <h3 className="font-medium mb-2">Delivery Address</h3>
                      <p className="text-sm text-muted-foreground">
                        {contactDetails.fullName}<br />
                        {contactDetails.address}<br />
                        {contactDetails.city}, {contactDetails.state} {contactDetails.zipCode}<br />
                        {contactDetails.phone}
                      </p>
                      <Button 
                        variant="link" 
                        onClick={() => setCurrentStep(1)}
                        className="p-0 h-auto mt-2"
                      >
                        Edit Details
                      </Button>
                    </div>
                  )}

                  {/* Payment Method Selection */}
                  <div>
                    <Label className="text-base font-medium">Payment Method</Label>
                    <div className="grid grid-cols-3 gap-4 mt-3">
                      <Button
                        variant={paymentMethod === 'card' ? 'default' : 'outline'}
                        onClick={() => setPaymentMethod('card')}
                        className="p-4 h-auto flex flex-col gap-2"
                      >
                        <CreditCard className="h-6 w-6" />
                        <span>Credit Card</span>
                      </Button>
                      <Button
                        variant={paymentMethod === 'paypal' ? 'default' : 'outline'}
                        onClick={() => setPaymentMethod('paypal')}
                        className="p-4 h-auto flex flex-col gap-2"
                      >
                        <div className="w-6 h-6 bg-blue-600 rounded text-white text-xs flex items-center justify-center">
                          PP
                        </div>
                        <span>PayPal</span>
                      </Button>
                      <Button
                        variant={paymentMethod === 'wallet' ? 'default' : 'outline'}
                        onClick={() => setPaymentMethod('wallet')}
                        className="p-4 h-auto flex flex-col gap-2"
                      >
                        <div className="w-6 h-6 bg-green-600 rounded text-white text-xs flex items-center justify-center">
                          W
                        </div>
                        <span>Digital Wallet</span>
                      </Button>
                    </div>
                  </div>

                  {/* Credit Card Form */}
                  {paymentMethod === 'card' && (
                    <div className="space-y-4">
                      <div>
                        <Label htmlFor="cardName">Cardholder Name</Label>
                        <Input
                          id="cardName"
                          value={cardDetails.name}
                          onChange={(e) => setCardDetails({...cardDetails, name: e.target.value})}
                          placeholder="John Doe"
                        />
                      </div>
                      <div>
                        <Label htmlFor="cardNumber">Card Number</Label>
                        <Input
                          id="cardNumber"
                          value={cardDetails.number}
                          onChange={(e) => setCardDetails({...cardDetails, number: e.target.value})}
                          placeholder="1234 5678 9012 3456"
                          maxLength={19}
                        />
                      </div>
                      <div className="grid grid-cols-2 gap-4">
                        <div>
                          <Label htmlFor="expiry">Expiry Date</Label>
                          <Input
                            id="expiry"
                            value={cardDetails.expiry}
                            onChange={(e) => setCardDetails({...cardDetails, expiry: e.target.value})}
                            placeholder="MM/YY"
                            maxLength={5}
                          />
                        </div>
                        <div>
                          <Label htmlFor="cvv">CVV</Label>
                          <Input
                            id="cvv"
                            value={cardDetails.cvv}
                            onChange={(e) => setCardDetails({...cardDetails, cvv: e.target.value})}
                            placeholder="123"
                            maxLength={4}
                          />
                        </div>
                      </div>
                    </div>
                  )}

                  {/* PayPal */}
                  {paymentMethod === 'paypal' && (
                    <div className="text-center py-8">
                      <div className="text-4xl text-blue-600 mb-4">PayPal</div>
                      <p className="text-muted-foreground">You will be redirected to PayPal to complete your payment securely.</p>
                    </div>
                  )}

                  {/* Digital Wallet */}
                  {paymentMethod === 'wallet' && (
                    <div className="text-center py-8">
                      <div className="text-4xl text-green-600 mb-4">💳</div>
                      <p className="text-muted-foreground">Pay using your digital wallet (Apple Pay, Google Pay, etc.)</p>
                    </div>
                  )}

                  {/* Security Notice */}
                  <div className="bg-muted/30 p-4 rounded-lg">
                    <div className="flex items-center gap-2 mb-2">
                      <Shield className="h-5 w-5 text-green-600" />
                      <span className="font-medium">Secure Payment</span>
                    </div>
                    <p className="text-sm text-muted-foreground">
                      Your payment information is encrypted and processed securely. We never store your card details.
                    </p>
                  </div>

                  {/* Terms */}
                  <div className="text-sm text-muted-foreground">
                    <p>
                      By completing this payment, you agree to our{' '}
                      <a href="#" className="text-primary hover:underline">Terms of Service</a>{' '}
                      and{' '}
                      <a href="#" className="text-primary hover:underline">Privacy Policy</a>.
                    </p>
                  </div>

                  {/* Order Total */}
                  <div className="bg-muted/50 p-4 rounded-lg">
                    <div className="flex justify-between font-bold text-lg">
                      <span>Total Amount</span>
                      <span>${paymentData.fees.total.toFixed(2)}</span>
                    </div>
                  </div>

                  {/* Payment Button */}
                  <Button 
                    onClick={handlePayment}
                    className="w-full bg-gradient-primary py-6 text-lg"
                    size="lg"
                  >
                    <CheckCircle className="mr-2 h-5 w-5" />
                    Complete Payment - ${paymentData.fees.total.toFixed(2)}
                  </Button>
                </CardContent>
              </Card>
            )}

            {/* What happens next info */}
            {currentStep === 2 && (
              <Card className="mt-6">
                <CardHeader>
                  <CardTitle className="flex items-center gap-2">
                    <Clock className="h-5 w-5" />
                    What happens next?
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-3 text-sm">
                    <div className="flex items-start gap-3">
                      <div className="w-6 h-6 bg-primary text-primary-foreground rounded-full flex items-center justify-center text-xs font-bold">
                        1
                      </div>
                      <div>
                        <p className="font-medium">Instant Confirmation</p>
                        <p className="text-muted-foreground">
                          You'll receive an email confirmation immediately after payment.
                        </p>
                      </div>
                    </div>
                    <div className="flex items-start gap-3">
                      <div className="w-6 h-6 bg-primary text-primary-foreground rounded-full flex items-center justify-center text-xs font-bold">
                        2
                      </div>
                      <div>
                        <p className="font-medium">
                          {paymentType === 'shop' ? 'Order Processing' : 'Facility Contact'}
                        </p>
                        <p className="text-muted-foreground">
                          {paymentType === 'shop' 
                            ? 'The shop owner will contact you within 24 hours to arrange delivery.'
                            : 'The facility manager will contact you 24 hours before your booking.'
                          }
                        </p>
                      </div>
                    </div>
                    <div className="flex items-start gap-3">
                      <div className="w-6 h-6 bg-primary text-primary-foreground rounded-full flex items-center justify-center text-xs font-bold">
                        3
                      </div>
                      <div>
                        <p className="font-medium">
                          {paymentType === 'shop' ? 'Enjoy Your Purchase' : 'Enjoy Your Game'}
                        </p>
                        <p className="text-muted-foreground">
                          {paymentType === 'shop'
                            ? 'Your order will be delivered to your specified address.'
                            : 'Arrive 15 minutes early and present your booking confirmation.'
                          }
                        </p>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default UnifiedPayment;