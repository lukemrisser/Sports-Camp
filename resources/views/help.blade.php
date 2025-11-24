<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help - {{ config('app.name', 'Falcon Teams') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.header', [
        'title' => 'Help Center',
        'title_class' => 'welcome-title',
    ])

    <div class="container">
        <!-- FAQ Section -->
        <div class="faq-section">
            <h2>How Can We Help You?</h2>
            <div class="faq-container">
                <!-- Registration Help -->
                <div class="faq-item" data-faq="0">
                    <div class="faq-question" onclick="toggleFaq(0)">
                        <h3>🏕️ Camp Registration</h3>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer" id="faq-0">
                        <div class="help-content">
                            <h4>How to Register for a Camp</h4>
                            <ol>
                                <li>Create an account or log in to your existing account</li>
                                <li>Browse available sports camps from the home page</li>
                                <li>Select a sport to view camp details and schedules</li>
                                <li>Click "Register" on your desired camp</li>
                                <li>Fill out your child's information</li>
                                <li>Complete payment to secure your spot</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Payment Help -->
                <div class="faq-item" data-faq="1">
                    <div class="faq-question" onclick="toggleFaq(1)">
                        <h3>💳 Payment & Billing</h3>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer" id="faq-1">
                        <div class="help-content">
                            <h4>Payment Options</h4>
                            <p>We accept:</p>
                            <ul>
                                <li>Credit Cards (Visa, MasterCard, American Express)</li>
                                <li>Debit Cards</li>
                            </ul>
                            <h4>Payment Security</h4>
                            <p>All payments are processed securely through Stripe. We do not store your payment information on our servers.</p>
                        </div>
                    </div>
                </div>

                <!-- Account Management -->
                <div class="faq-item" data-faq="2">
                    <div class="faq-question" onclick="toggleFaq(2)">
                        <h3>👤 Account Management</h3>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer" id="faq-2">
                        <div class="help-content">
                            <h4>Click the "Profile" button to manage your account</h4>
                            <ul>
                                <li>Update your contact information</li>
                                <li>Add or edit your children's information</li>
                                <li>View your registration history</li>
                                <li>Manage emergency contacts</li>
                            </ul>
                            <h4>Password & Security</h4>
                            <p>You can change your password at any time from your profile page. We recommend using a strong, unique password for your account.</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="faq-item" data-faq="4">
                    <div class="faq-question" onclick="toggleFaq(4)">
                        <h3>📞 Contact Us</h3>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer" id="faq-4">
                        <div class="help-content">
                            <div class="contact-grid">
                                <div>
                                    <h4>General Support</h4>
                                    <p><strong>Email:</strong> support@falconteams.com</p>
                                    <p><strong>Phone:</strong> (555) 123-CAMP</p>
                                    <p><strong>Hours:</strong> Mon-Fri 8AM-6PM</p>
                                </div>
                                <div>
                                    <h4>Emergency Contact</h4>
                                    <p>During camp hours only:</p>
                                    <p><strong>Emergency:</strong> (555) 123-HELP</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Need More Help -->
            <div class="help-contact-section">
                <h3>Still Need Help?</h3>
                <p>Can't find what you're looking for? Our support team is here to help!</p>
                <a href="mailto:support@falconteams.com" class="contact-button">Contact Support</a>
            </div>
        </div>
    </div>

    <style>
        /* FAQ Section */
        .faq-section {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .faq-section h2 {
            color: #1f2937;
            margin-bottom: 30px;
            font-size: 1.8rem;
            font-weight: 600;
            text-align: center;
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 20px;
        }

        .faq-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .faq-question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            padding: 20px 0;
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            color: #3b82f6;
        }

        .faq-question h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
            flex: 1;
            padding-right: 20px;
        }

        .faq-question:hover h3 {
            color: #3b82f6;
        }

        .faq-toggle {
            font-size: 1.5rem;
            font-weight: bold;
            color: #6b7280;
            transition: all 0.3s ease;
            transform-origin: center;
        }

        .faq-toggle.open {
            transform: rotate(45deg);
            color: #3b82f6;
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            padding: 0;
        }

        .faq-answer.open {
            max-height: 600px;
            padding: 0 0 20px 0;
            transition: max-height 0.4s ease-in;
        }

        .help-content {
            color: #4b5563;
            line-height: 1.6;
        }

        .help-content h4 {
            color: #374151;
            font-size: 1.1rem;
            font-weight: 600;
            margin: 15px 0 10px 0;
        }

        .help-content h4:first-child {
            margin-top: 0;
        }

        .help-content p {
            margin: 8px 0;
        }

        .help-content ul, .help-content ol {
            margin: 10px 0;
            padding-left: 20px;
        }

        .help-content ul {
            list-style-type: disc;
        }

        .help-content ol {
            list-style-type: decimal;
        }

        .help-content li {
            margin: 5px 0;
            display: list-item;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 10px;
        }

        .help-contact-section {
            margin-top: 40px;
            text-align: center;
            padding: 30px;
            background: #f9fafb;
            border-radius: 8px;
            border-top: 1px solid #e5e7eb;
        }

        .help-contact-section h3 {
            color: #1f2937;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .help-contact-section p {
            color: #6b7280;
            margin-bottom: 20px;
        }

        .contact-button {
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            display: inline-block;
            transition: background-color 0.3s ease;
            font-weight: 500;
        }

        .contact-button:hover {
            background-color: #2563eb;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .faq-section {
                padding: 30px 20px;
                margin: 0 10px;
            }

            .faq-section h2 {
                font-size: 1.5rem;
            }

            .faq-question h3 {
                font-size: 1rem;
            }

            .contact-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .help-contact-section {
                padding: 25px 15px;
            }
        }

        @media (max-width: 480px) {
            .faq-section {
                padding: 30px 15px;
            }

            .faq-answer.open {
                max-height: 800px;
            }
        }
    </style>

    <script>
        // FAQ Toggle Functionality
        function toggleFaq(index) {
            const answer = document.getElementById(`faq-${index}`);
            const toggle = document.querySelector(`[data-faq="${index}"] .faq-toggle`);
            
            if (answer && toggle) {
                if (answer.classList.contains('open')) {
                    answer.classList.remove('open');
                    toggle.classList.remove('open');
                } else {
                    // Close all other FAQs
                    document.querySelectorAll('.faq-answer').forEach(item => {
                        item.classList.remove('open');
                    });
                    document.querySelectorAll('.faq-toggle').forEach(item => {
                        item.classList.remove('open');
                    });
                    
                    // Open the clicked FAQ
                    answer.classList.add('open');
                    toggle.classList.add('open');
                }
            }
        }
    </script>

    @include('partials.footer')
</body>

</html>