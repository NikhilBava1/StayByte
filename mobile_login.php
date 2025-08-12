<div class="tourmaster-lightbox-content">
                                <form class="tourmaster-login-form tourmaster-form-field tourmaster-with-border" method="post" action="API/login_API.php" id="login-form-mobile">
                                    <div class="tourmaster-login-form-fields clearfix">
                                        <p class="tourmaster-login-user">
                                            <label>Username or E-Mail</label>
                                            <input type="text" name="username" />
                                        </p>
                                        <p class="tourmaster-login-pass">
                                            <label>Password</label>
                                            <input type="password" name="password" />
                                        </p>
                                        
                                    </div>
                                    
                                    <p class="tourmaster-login-submit">
                                        <input type="submit" name="wp-submit" class="tourmaster-button" value="Sign In!" />
                                    </p>
                                    <p class="tourmaster-login-lost-password">
                                        <a href="#">Forget Password?</a>
                                    </p>

                                </form>

                                <div class="tourmaster-login-bottom">
                                    <h3 class="tourmaster-login-bottom-title">Do not have an account?</h3>
                                    <a class="tourmaster-login-bottom-link" href="register.php">Create an Account</a>
                                </div>
                            </div>

                            <script>
                                            fetch('API/role_API.php')
                                            .then(response => response.json())
                                            .then(data => {
                                                console.log(data);
                                                const roleSelect = document.getElementById('role');
                                                data.forEach(role => {
                                                    const option = document.createElement('option');
                                                    option.value = role.role_id;
                                                    option.textContent = role.role_name;
                                                    roleSelect.appendChild(option); 
                                                });
                                            })
                                            .catch(error => {
                                                console.error('Error:', error);
                                            }); 

                                                document.getElementById('login-form-mobile').addEventListener('submit', function(e) {
                                                e.preventDefault();
                                                const formData = new FormData(this);
                                                const data = {
                                                    username: formData.get('username'),
                                                    password: formData.get('password'),
                                                    role: formData.get('role')
                                                };
                                                console.log('Form data:', {
                                                    username: formData.get('username'),
                                                    password: formData.get('password'),
                                                    role: formData.get('role')
                                                });
                                                console.log('Sending data:', data);
                                                console.log('JSON string:', JSON.stringify(data));
                                                
                                                fetch('API/login_API.php', {
                                                    method: 'POST',
                                                    body: JSON.stringify(data),
                                                    headers: {
                                                        'Content-Type': 'application/json'
                                                    }
                                                })
                                                .then(response => response.json())
                                                .then(result => {
                                                    console.log('Login response:', result);
                                                    
                                                    if (result.success) {
                                                        // Show success message
                                                        alert('Login successful! Redirecting to ' + result.redirect);
                                                        // Redirect to appropriate dashboard
                                                        window.location.href = result.redirect;
                                                    } else {
                                                        // Show error message
                                                        alert('Login failed: ' + result.error);
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('Error:', error);
                                                    alert('An error occurred during login');
                                                });
                                            });     
                                                </script>