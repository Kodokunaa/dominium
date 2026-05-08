    <footer class="bg-gray-900 text-gray-300 mt-20 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-8">
                <div>
                    <div class="brand text-white text-2xl font-bold mb-2">Dominium</div>
                    <p class="text-sm">Premium rental marketplace</p>
                </div>

                <div>
                    <h4 class="font-semibold text-white mb-4">Browse</h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="<?= url('listings') ?>" class="hover:text-gray-200 transition">
                                Listings
                            </a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-white mb-4">Account</h4>
                    <ul class="space-y-2 text-sm">
                        <?php if (is_authenticated()): ?>
                            <li>
                                <a href="<?= url('dashboard') ?>" class="hover:text-gray-200 transition">
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="<?= url('profile') ?>" class="hover:text-gray-200 transition">
                                    Profile
                                </a>
                            </li>
                            <li>
                                <a href="<?= url('logout') ?>" class="hover:text-gray-200 transition">
                                    Logout
                                </a>
                            </li>
                        <?php else: ?>
                            <li>
                                <a href="<?= url('login') ?>" class="hover:text-gray-200 transition">
                                    Login
                                </a>
                            </li>
                            <li>
                                <a href="<?= url('register') ?>" class="hover:text-gray-200 transition">
                                    Register
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold text-white mb-4">Support</h4>
                    <p class="text-sm">support@dominium.local</p>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 text-center text-sm text-gray-400">
                <p>&copy; <?= date('Y') ?> Dominium. All rights reserved. School project showcase.</p>
            </div>
        </div>
    </footer>

    <!-- Stripe JS -->
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Booking page-specific code only runs if these elements exist.
            var checkinInput = document.getElementById('checkin');
            var checkoutInput = document.getElementById('checkout');
            var totalEl = document.getElementById('total');

            var cardElement = document.getElementById('card-element');
            var submitButton = document.getElementById('submit-button');
            var zipInput = document.getElementById('zip');
            var cardErrors = document.getElementById('card-errors');

            /*
             * Booking total calculator
             */
            if (checkinInput && checkoutInput && totalEl) {
                function calculateTotal() {
                    var checkin = checkinInput.value;
                    var checkout = checkoutInput.value;
                    var pricePerNight = <?= isset($listing) ? (float) $listing['price'] : 0 ?>;

                    if (!checkin || !checkout) {
                        return;
                    }

                    var checkinDate = new Date(checkin);
                    var checkoutDate = new Date(checkout);
                    var nights = Math.ceil((checkoutDate - checkinDate) / (1000 * 60 * 60 * 24));

                    if (nights > 0) {
                        var total = pricePerNight * nights;
                        totalEl.textContent = '₱' + total.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }

                checkinInput.addEventListener('change', calculateTotal);
                checkoutInput.addEventListener('change', calculateTotal);
                calculateTotal();
            }

            /*
             * Payment integration
             * Supports:
             * 1. Mock/demo payment keys: custom visible demo fields
             * 2. Real Stripe test keys: split Card Number / Expiry / CVC fields
             * 3. Fallback: old single Stripe card element
             */
            if (cardElement && submitButton && zipInput && cardErrors) {
                var stripeKey = '<?= esc(STRIPE_PUBLISHABLE_KEY) ?>';
                var isMockStripe = !stripeKey || stripeKey.indexOf('pk_test_mock_') === 0;
                var form = cardElement.closest('form') || document.querySelector('form');

                function setError(message) {
                    cardErrors.textContent = message || '';
                }

                function addOrUpdateHiddenInput(name, value) {
                    var input = form.querySelector('input[name="' + name + '"]');

                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = name;
                        form.appendChild(input);
                    }

                    input.value = value;
                }

                /*
                 * MOCK / DEMO PAYMENT MODE
                 * This shows normal input fields and does not charge real money.
                 */
                if (isMockStripe) {
                    cardElement.classList.add('demo-payment-wrapper');
                    cardElement.classList.remove('px-4', 'py-3');

                    cardElement.innerHTML = `
                        <div class="demo-payment-fields">
                            <div class="demo-payment-field demo-payment-field-full">
                                <label for="demo-card-number">Card Number</label>
                                <input
                                    type="text"
                                    id="demo-card-number"
                                    inputmode="numeric"
                                    autocomplete="cc-number"
                                    maxlength="19"
                                    placeholder="4242 4242 4242 4242"
                                >
                            </div>

                            <div class="demo-payment-grid">
                                <div class="demo-payment-field">
                                    <label for="demo-card-expiry">Expiry Date</label>
                                    <input
                                        type="text"
                                        id="demo-card-expiry"
                                        inputmode="numeric"
                                        autocomplete="cc-exp"
                                        maxlength="5"
                                        placeholder="12/30"
                                    >
                                </div>

                                <div class="demo-payment-field">
                                    <label for="demo-card-cvc">CVC</label>
                                    <input
                                        type="text"
                                        id="demo-card-cvc"
                                        inputmode="numeric"
                                        autocomplete="cc-csc"
                                        maxlength="4"
                                        placeholder="123"
                                    >
                                </div>
                            </div>

                            <div class="demo-payment-notice">
                                Demo payment mode only. No real money will be charged.
                            </div>
                        </div>
                    `;

                    var demoCardNumber = document.getElementById('demo-card-number');
                    var demoCardExpiry = document.getElementById('demo-card-expiry');
                    var demoCardCvc = document.getElementById('demo-card-cvc');

                    if (demoCardNumber) {
                        demoCardNumber.addEventListener('input', function () {
                            var value = demoCardNumber.value.replace(/\D/g, '').slice(0, 16);
                            demoCardNumber.value = value.replace(/(.{4})/g, '$1 ').trim();
                        });
                    }

                    if (demoCardExpiry) {
                        demoCardExpiry.addEventListener('input', function () {
                            var value = demoCardExpiry.value.replace(/\D/g, '').slice(0, 4);

                            if (value.length >= 3) {
                                value = value.slice(0, 2) + '/' + value.slice(2);
                            }

                            demoCardExpiry.value = value;
                        });
                    }

                    if (demoCardCvc) {
                        demoCardCvc.addEventListener('input', function () {
                            demoCardCvc.value = demoCardCvc.value.replace(/\D/g, '').slice(0, 4);
                        });
                    }

                    form.addEventListener('submit', function (event) {
                        var cardNumber = (demoCardNumber.value || '').replace(/\D/g, '');
                        var expiry = (demoCardExpiry.value || '').trim();
                        var cvc = (demoCardCvc.value || '').replace(/\D/g, '');
                        var zip = (zipInput.value || '').trim();

                        setError('');

                        if (cardNumber.length < 12) {
                            event.preventDefault();
                            setError('Please enter a valid demo card number.');
                            demoCardNumber.focus();
                            return;
                        }

                        if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
                            event.preventDefault();
                            setError('Please enter expiry date in MM/YY format.');
                            demoCardExpiry.focus();
                            return;
                        }

                        if (cvc.length < 3) {
                            event.preventDefault();
                            setError('Please enter a valid CVC.');
                            demoCardCvc.focus();
                            return;
                        }

                        if (!zip) {
                            event.preventDefault();
                            setError('Please enter a ZIP code.');
                            zipInput.focus();
                            return;
                        }

                        var last4 = cardNumber.slice(-4);

                        addOrUpdateHiddenInput('stripeToken', 'tok_dominium_demo_' + last4 + '_' + Date.now());
                        addOrUpdateHiddenInput('demo_card_last4', last4);
                        addOrUpdateHiddenInput('demo_card_brand', 'Demo Card');
                        addOrUpdateHiddenInput('demo_billing_zip', zip);
                    });

                    return;
                }

                /*
                 * REAL STRIPE TEST MODE
                 * This uses split Stripe Elements if the booking page has:
                 * #card-number-element
                 * #card-expiry-element
                 * #card-cvc-element
                 */
                if (typeof Stripe === 'undefined') {
                    setError('Stripe.js failed to load. Please refresh the page.');
                    submitButton.disabled = true;
                    return;
                }

                var stripe = Stripe(stripeKey);
                var elements = stripe.elements();

                var stripeStyle = {
                    base: {
                        fontSize: '16px',
                        color: '#0F172A',
                        fontFamily: 'Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
                        '::placeholder': {
                            color: '#94A3B8'
                        }
                    },
                    invalid: {
                        color: '#B91C1C'
                    }
                };

                var cardNumberContainer = document.getElementById('card-number-element');
                var cardExpiryContainer = document.getElementById('card-expiry-element');
                var cardCvcContainer = document.getElementById('card-cvc-element');

                var cardNumber;

                if (cardNumberContainer && cardExpiryContainer && cardCvcContainer) {
                    cardNumber = elements.create('cardNumber', {
                        style: stripeStyle,
                        placeholder: '4242 4242 4242 4242'
                    });

                    var cardExpiry = elements.create('cardExpiry', {
                        style: stripeStyle,
                        placeholder: 'MM / YY'
                    });

                    var cardCvc = elements.create('cardCvc', {
                        style: stripeStyle,
                        placeholder: 'CVC'
                    });

                    cardNumber.mount('#card-number-element');
                    cardExpiry.mount('#card-expiry-element');
                    cardCvc.mount('#card-cvc-element');
                } else {
                    cardNumber = elements.create('card', {
                        hidePostalCode: true,
                        style: stripeStyle
                    });

                    cardNumber.mount('#card-element');
                }

                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    setError('');

                    if (!zipInput.value.trim()) {
                        setError('Please enter your ZIP code.');
                        zipInput.focus();
                        return;
                    }

                    stripe.createToken(cardNumber, {
                        address_zip: zipInput.value.trim()
                    }).then(function (result) {
                        if (result.error) {
                            setError(result.error.message);
                        } else {
                            addOrUpdateHiddenInput('stripeToken', result.token.id);
                            form.submit();
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>