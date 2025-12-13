@extends('layouts.plan')

@section('content')
    <div class="container">
        <header class="header">
            <h1 class="header-title">Choose Your Plan</h1>
            <p class="header-subtitle">Select the perfect plan that fits your needs</p>
        </header>

        <div class="pricing-cards">
            <!-- Basic Plan Card -->
            <div class="card card-basic">
                <div class="card-header">
                    <div class="plan-badge">Starter</div>
                    <h2 class="plan-name">{{ $basic->name }}</h2>
                    <div class="price-container">
                        <span class="currency">Rp</span>
                        <span class="price">{{ number_format($basic->price, 0, ',', '.') }}</span>
                        <span class="period">/month</span>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="features-list">
                        @foreach ($basic->features as $key => $value)
                            <li class="feature-item">
                                <svg class="feature-icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>{{ ucfirst(str_replace('_', ' ', $key)) }}:
                                    <strong>{{ is_bool($value) ? ($value ? 'Ya' : 'Tidak') : $value }}</strong></span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-footer">
                    <button class="btn btn-basic" id="btn-basic">
                        <span class="btn-text">Get Started</span>
                        <svg class="btn-arrow" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Pro Plan Card -->
            <div class="card card-pro">
                <div class="card-header">
                    <div class="plan-badge plan-badge-pro">Professional</div>
                    <h2 class="plan-name">{{ $pro->name }}</h2>
                    <div class="price-container">
                        <span class="currency">Rp</span>
                        <span class="price">{{ number_format($pro->price, 0, ',', '.') }}</span>
                        <span class="period">/month</span>
                    </div>
                </div>

                <div class="card-body">
                    <ul class="features-list">
                        @foreach ($pro->features as $key => $value)
                            <li class="feature-item">
                                <svg class="feature-icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>{{ ucfirst(str_replace('_', ' ', $key)) }}:
                                    <strong>{{ is_bool($value) ? ($value ? 'Ya' : 'Tidak') : $value }}</strong></span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="card-footer">
                    <button class="btn btn-pro" id="pay-pro">
                        <span class="btn-text">Upgrade to Pro</span>
                        <svg class="btn-arrow" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- MIDTRANS POPUP SCRIPT --}}
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        document.getElementById('pay-pro').addEventListener('click', function() {
            fetch('/billing/pay', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        plan: 'pro'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    snap.pay(data.token);
                });
        });
    </script>
@endsection
