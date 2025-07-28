import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Upload, User, Building, Award, FileText, CheckCircle } from 'lucide-react';
import { useToast } from '@/hooks/use-toast';

const ServiceProviderRegister = () => {
  const [step, setStep] = useState(1);
  const [providerType, setProviderType] = useState('');
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    businessName: '',
    description: '',
    location: '',
    experience: '',
    specialties: [],
    photos: [],
    certificates: [],
    idCard: null,
    businessLicense: null
  });
  const { toast } = useToast();

  const providerTypes = [
    { value: 'ground', label: 'Ground Owner', icon: Building },
    { value: 'coach', label: 'Sports Coach', icon: User },
    { value: 'shop', label: 'Shop Owner', icon: Building }
  ];

  const handleFileUpload = (field: string, files: FileList | null) => {
    if (files) {
      const fileArray = Array.from(files);
      setFormData(prev => ({
        ...prev,
        [field]: field === 'photos' || field === 'certificates' ? [...prev[field], ...fileArray] : fileArray[0]
      }));
    }
  };

  const handleSubmit = () => {
    // Create application object
    const application = {
      id: Date.now().toString(),
      type: providerType,
      personalInfo: {
        name: formData.name,
        email: formData.email,
        phone: formData.phone,
        location: formData.location
      },
      businessInfo: {
        businessName: formData.businessName,
        description: formData.description,
        experience: formData.experience,
        specialties: formData.specialties
      },
      documents: {
        photos: formData.photos.map(file => ({ name: file.name, size: file.size, type: file.type })),
        certificates: formData.certificates.map(file => ({ name: file.name, size: file.size, type: file.type })),
        idCard: formData.idCard ? { name: formData.idCard.name, size: formData.idCard.size, type: formData.idCard.type } : null,
      },
      status: 'pending',
      submittedDate: new Date().toISOString(),
      reviewedBy: null,
      reviewDate: null,
      reviewNotes: ''
    };

    // Store in localStorage (simulating backend storage)
    const existingApplications = JSON.parse(localStorage.getItem('serviceProviderApplications') || '[]');
    existingApplications.push(application);
    localStorage.setItem('serviceProviderApplications', JSON.stringify(existingApplications));

    toast({
      title: "Application Submitted!",
      description: "Your application has been submitted for review. We'll contact you within 24-48 hours.",
    });
    setStep(5);
  };

  const renderStep1 = () => (
    <div className="space-y-6">
      <h2 className="text-2xl font-bold text-center">Choose Your Service Type</h2>
      <div className="grid gap-4">
        {providerTypes.map((type) => (
          <Card 
            key={type.value} 
            className={`cursor-pointer transition-all duration-200 hover:shadow-medium ${
              providerType === type.value ? 'ring-2 ring-primary bg-primary/5' : ''
            }`}
            onClick={() => setProviderType(type.value)}
          >
            <CardContent className="flex items-center p-6">
              <type.icon className="h-8 w-8 mr-4 text-primary" />
              <div>
                <h3 className="font-semibold">{type.label}</h3>
                <p className="text-sm text-muted-foreground">
                  {type.value === 'ground' && 'Rent out sports facilities and courts'}
                  {type.value === 'coach' && 'Offer professional sports training'}
                  {type.value === 'shop' && 'Sell sports equipment and gear'}
                </p>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );

  const renderStep2 = () => (
    <div className="space-y-6">
      <h2 className="text-2xl font-bold text-center">Personal Information</h2>
      <div className="grid md:grid-cols-2 gap-4">
        <div>
          <Label htmlFor="name">Full Name *</Label>
          <Input
            id="name"
            value={formData.name}
            onChange={(e) => setFormData(prev => ({ ...prev, name: e.target.value }))}
            placeholder="Enter your full name"
          />
        </div>
        <div>
          <Label htmlFor="email">Email Address *</Label>
          <Input
            id="email"
            type="email"
            value={formData.email}
            onChange={(e) => setFormData(prev => ({ ...prev, email: e.target.value }))}
            placeholder="Enter your email"
          />
        </div>
        <div>
          <Label htmlFor="phone">Phone Number *</Label>
          <Input
            id="phone"
            value={formData.phone}
            onChange={(e) => setFormData(prev => ({ ...prev, phone: e.target.value }))}
            placeholder="Enter your phone number"
          />
        </div>
        <div>
          <Label htmlFor="location">Location *</Label>
          <Input
            id="location"
            value={formData.location}
            onChange={(e) => setFormData(prev => ({ ...prev, location: e.target.value }))}
            placeholder="City, State"
          />
        </div>
      </div>
      <div>
        <Label htmlFor="businessName">
          {providerType === 'ground' ? 'Facility Name' : 
           providerType === 'coach' ? 'Coaching Business Name' : 
           'Shop Name'} *
        </Label>
        <Input
          id="businessName"
          value={formData.businessName}
          onChange={(e) => setFormData(prev => ({ ...prev, businessName: e.target.value }))}
          placeholder={`Enter your ${providerType} name`}
        />
      </div>
      <div>
        <Label htmlFor="description">Description *</Label>
        <Textarea
          id="description"
          value={formData.description}
          onChange={(e) => setFormData(prev => ({ ...prev, description: e.target.value }))}
          placeholder="Describe your services, facilities, or expertise"
          rows={4}
        />
      </div>
    </div>
  );

  const renderStep3 = () => (
    <div className="space-y-6">
      <h2 className="text-2xl font-bold text-center">Professional Details</h2>
      <div>
        <Label htmlFor="experience">Years of Experience</Label>
        <Select value={formData.experience} onValueChange={(value) => setFormData(prev => ({ ...prev, experience: value }))}>
          <SelectTrigger>
            <SelectValue placeholder="Select experience level" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="1-2">1-2 years</SelectItem>
            <SelectItem value="3-5">3-5 years</SelectItem>
            <SelectItem value="6-10">6-10 years</SelectItem>
            <SelectItem value="10+">10+ years</SelectItem>
          </SelectContent>
        </Select>
      </div>
      
      {providerType === 'coach' && (
        <div>
          <Label>Specialties</Label>
          <div className="grid grid-cols-2 gap-2 mt-2">
            {['Football', 'Basketball', 'Tennis', 'Swimming', 'Fitness', 'Yoga'].map((sport) => (
              <Button
                key={sport}
                variant={formData.specialties.includes(sport) ? "default" : "outline"}
                size="sm"
                onClick={() => {
                  setFormData(prev => ({
                    ...prev,
                    specialties: prev.specialties.includes(sport)
                      ? prev.specialties.filter(s => s !== sport)
                      : [...prev.specialties, sport]
                  }));
                }}
              >
                {sport}
              </Button>
            ))}
          </div>
        </div>
      )}

      <div>
        <Label>Upload Photos *</Label>
        <div className="border-2 border-dashed border-border rounded-lg p-8 text-center">
          <Upload className="h-8 w-8 mx-auto mb-2 text-muted-foreground" />
          <p className="text-sm text-muted-foreground mb-2">
            Upload photos of your {providerType === 'ground' ? 'facility' : providerType === 'coach' ? 'training sessions' : 'shop'}
          </p>
          <Input
            type="file"
            multiple
            accept="image/*"
            onChange={(e) => handleFileUpload('photos', e.target.files)}
            className="hidden"
            id="photos"
          />
          <Button variant="outline" onClick={() => document.getElementById('photos')?.click()}>
            Choose Files
          </Button>
          {formData.photos.length > 0 && (
            <div className="mt-2">
              {formData.photos.map((file, index) => (
                <Badge key={index} variant="secondary" className="mr-1">
                  {file.name}
                </Badge>
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );

  const renderStep4 = () => (
    <div className="space-y-6">
      <h2 className="text-2xl font-bold text-center">Documents & Verification</h2>
      
      <div>
        <Label>Upload ID Card/Passport *</Label>
        <div className="border-2 border-dashed border-border rounded-lg p-6 text-center">
          <FileText className="h-6 w-6 mx-auto mb-2 text-muted-foreground" />
          <Input
            type="file"
            accept="image/*,.pdf"
            onChange={(e) => handleFileUpload('idCard', e.target.files)}
            className="hidden"
            id="idCard"
          />
          <Button variant="outline" onClick={() => document.getElementById('idCard')?.click()}>
            Upload ID Card
          </Button>
          {formData.idCard && (
            <Badge variant="secondary" className="mt-2">{formData.idCard.name}</Badge>
          )}
        </div>
      </div>

      <div>
        <Label>Certificates & Licenses</Label>
        <div className="border-2 border-dashed border-border rounded-lg p-6 text-center">
          <Award className="h-6 w-6 mx-auto mb-2 text-muted-foreground" />
          <p className="text-sm text-muted-foreground mb-2">
            Upload relevant certificates, licenses, or qualifications
          </p>
          <Input
            type="file"
            multiple
            accept="image/*,.pdf"
            onChange={(e) => handleFileUpload('certificates', e.target.files)}
            className="hidden"
            id="certificates"
          />
          <Button variant="outline" onClick={() => document.getElementById('certificates')?.click()}>
            Upload Certificates
          </Button>
          {formData.certificates.length > 0 && (
            <div className="mt-2">
              {formData.certificates.map((file, index) => (
                <Badge key={index} variant="secondary" className="mr-1">
                  {file.name}
                </Badge>
              ))}
            </div>
          )}
        </div>
      </div>

      <div className="bg-muted/50 p-4 rounded-lg">
        <h4 className="font-medium mb-2">Review Your Application</h4>
        <div className="space-y-1 text-sm">
          <div><strong>Type:</strong> {providerTypes.find(t => t.value === providerType)?.label}</div>
          <div><strong>Name:</strong> {formData.name}</div>
          <div><strong>Business:</strong> {formData.businessName}</div>
          <div><strong>Email:</strong> {formData.email}</div>
          <div><strong>Phone:</strong> {formData.phone}</div>
        </div>
      </div>
    </div>
  );

  const renderStep5 = () => (
    <div className="text-center space-y-6">
      <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto">
        <CheckCircle className="h-10 w-10 text-green-600" />
      </div>
      <h2 className="text-2xl font-bold text-green-600">Application Submitted!</h2>
      <p className="text-muted-foreground max-w-md mx-auto">
        Thank you for your application. Our team will review your submission and contact you within 24-48 hours.
      </p>
      <Button onClick={() => window.location.href = '/'}>
        Return to Home
      </Button>
    </div>
  );

  return (
    <div className="min-h-screen bg-background py-12">
      <div className="container mx-auto px-4 max-w-2xl">
        <div className="text-center mb-8">
          <h1 className="text-3xl font-bold text-foreground mb-2">
            Service Provider Registration
          </h1>
          <div className="flex justify-center items-center space-x-2 mb-6">
            {[1, 2, 3, 4].map((i) => (
              <div
                key={i}
                className={`w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium ${
                  i <= step ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'
                }`}
              >
                {i}
              </div>
            ))}
          </div>
        </div>

        <Card>
          <CardContent className="p-8">
            {step === 1 && renderStep1()}
            {step === 2 && renderStep2()}
            {step === 3 && renderStep3()}
            {step === 4 && renderStep4()}
            {step === 5 && renderStep5()}

            {step < 5 && (
              <div className="flex justify-between mt-8">
                <Button
                  variant="outline"
                  onClick={() => setStep(step - 1)}
                  disabled={step === 1}
                >
                  Previous
                </Button>
                <Button
                  onClick={() => {
                    if (step === 4) {
                      handleSubmit();
                    } else {
                      setStep(step + 1);
                    }
                  }}
                  disabled={step === 1 && !providerType}
                >
                  {step === 4 ? 'Submit Application' : 'Next'}
                </Button>
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default ServiceProviderRegister;