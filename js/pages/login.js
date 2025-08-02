// AuthContext-like functionality
        class AuthManager {
            constructor() {
                this.currentUser = this.getCurrentUser();
            }

            getCurrentUser() {
                const storedUser = localStorage.getItem('currentUser');
                return storedUser ? JSON.parse(storedUser) : null;
            }

            async login(email, password) {
                // Simulate API call delay
                await new Promise(resolve => setTimeout(resolve, 1500));
                
                // Get users from localStorage (matching your AuthContext logic)
                const users = JSON.parse(localStorage.getItem('users') || '[]');
                const foundUser = users.find(u => u.email === email && u.password === password);
                
                if (foundUser) {
                    const { password: _, ...userWithoutPassword } = foundUser;
                    this.currentUser = userWithoutPassword;
                    localStorage.setItem('currentUser', JSON.stringify(userWithoutPassword));
                    return { success: true };
                }
                return { success: false, error: 'Invalid credentials' };
            }

            logout() {
                this.currentUser = null;
                localStorage.removeItem('currentUser');
            }

            isAuthenticated() {
                return !!this.currentUser;
            }
        }

        // Initialize auth manager
        const auth = new AuthManager();

        // Toast functionality (matching your useToast hook)
        function showToast(title, description, variant = 'default') {
            const toastContainer = document.getElementById('toastContainer');
            
            const toast = document.createElement('div');
            toast.className = `toast ${variant === 'destructive' ? 'toast-destructive' : ''}`;
            
            toast.innerHTML = `
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-description">${description}</div>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            
            // Remove toast after 5 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        }

        // Form submission handler (matching your React component logic)
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const submitBtn = document.getElementById('submitBtn');
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span>Signing in...';
            
            try {
                const result = await auth.login(email, password);
                
                if (result.success) {
                    showToast(
                        'Welcome back!',
                        'You have successfully logged in.'
                    );
                    
                    // Redirect after successful login (matching your navigate('/'))
                    setTimeout(() => {
                        window.location.href = ''; // Replace with your dashboard URL
                    }, 2000);
                } else {
                    showToast(
                        'Login failed',
                        'Invalid email or password. Please try again.',
                        'destructive'
                    );
                }
            } catch (error) {
                showToast(
                    'Error',
                    'An error occurred. Please try again.',
                    'destructive'
                );
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Sign In';
            }
        });

        // Sample user data for testing (you can remove this in production)
        if (!localStorage.getItem('users')) {
            const sampleUsers = [
                {
                    id: '1',
                    name: 'John Doe',
                    email: 'user@example.com',
                    password: 'password',
                    phone: '+1234567890',
                    location: 'New York, NY',
                    bio: 'Passionate sports enthusiast',
                    role: 'Player',
                    joinDate: '2024-01-15',
                    sports: ['Basketball', 'Tennis'],
                    avatar: '👤'
                }
            ];
            localStorage.setItem('users', JSON.stringify(sampleUsers));
        }

        // Test credentials info
        console.log('Test credentials:');
        console.log('Email: user@example.com');
        console.log('Password: password');