<div class="vehicle-mega-menu-wrapper">
    <div class="vehicle-mega-menu shadow-sm rounded-3 border" style="background: #111827; border-color: #1e293b !important;">
        <!-- Menu Header -->
        <div class="p-2 px-3 border-bottom d-flex align-items-center justify-content-between rounded-top-3" style="background: #090d16; border-color: #1e293b !important;">
            <span class="text-uppercase fw-bold text-secondary font-sm" style="font-size: 11px; letter-spacing: 0.5px;">
                <i class="fa-solid fa-car-side text-danger me-1"></i> Popular Car Brands
            </span>
            <a href="{{ route('vehicles') }}" class="text-danger fw-bold font-sm text-decoration-none" style="font-size: 11px;">
                All ({{ $globalBrands->count() }}) &rarr;
            </a>
        </div>

        <!-- Brands List -->
        <div class="vehicle-menu-list position-relative">
            @foreach($globalBrands as $brand)
                @if($loop->iteration == 9)
                    <!-- Collapsible wrapper for remaining brands -->
                    <div class="collapse" id="moreVehicleBrands">
                @endif

                <div class="vehicle-menu-item" style="border-bottom: 1px solid #1e293b;">
                    <a href="{{ route('vehicle.cars', $brand->slug) }}" class="vehicle-menu-link">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset($brand->logo ?: 'images/brands/' . $brand->slug . '.svg') }}" 
                                 alt="{{ $brand->name }}" 
                                 class="brand-icon-img"
                                 style="width: 20px; height: 20px; object-fit: contain;">
                            <span class="brand-name font-sm fw-semibold text-white">{{ $brand->name }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            @if($brand->carModels->count() > 0)
                                <span class="badge bg-dark text-secondary border border-secondary border-opacity-25 font-sm" style="font-size: 10px;">{{ $brand->carModels->count() }}</span>
                            @endif
                            <i class="fa-solid fa-chevron-right text-secondary arrow-icon" style="font-size: 10px;"></i>
                        </div>
                    </a>

                    <!-- Flyout Panel for Company Cars & Variants -->
                    @if($brand->carModels->isNotEmpty())
                        <div class="vehicle-flyout-pane shadow-lg" style="background: #111827; border: 1px solid #1e293b; border-left: 3px solid var(--primary-red);">
                            <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom" style="border-color: #1e293b !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset($brand->logo ?: 'images/brands/' . $brand->slug . '.svg') }}" 
                                         alt="{{ $brand->name }}" 
                                         style="height: 24px; width: 36px; object-fit: contain;">
                                    <h6 class="fw-bold mb-0 text-white brand-font text-uppercase" style="font-size: 14px;">
                                        {{ $brand->name }} Cars & Variants
                                    </h6>
                                </div>
                                <a href="{{ route('vehicle.cars', $brand->slug) }}" class="text-danger fw-bold text-decoration-none font-sm" style="font-size: 12px;">
                                    All {{ $brand->name }} Models &rarr;
                                </a>
                            </div>

                            <div class="row row-cols-1 row-cols-md-2 g-2">
                                @foreach($brand->carModels as $car)
                                    <div class="col">
                                        <div class="flyout-car-box p-2 rounded border" style="background: #161f30; border-color: #1e293b !important;">
                                            <!-- Car Header -->
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <a href="{{ route('vehicle.variants', [$brand->slug, $car->slug]) }}" 
                                                   class="fw-bold text-white text-decoration-none font-sm hover-red d-flex align-items-center gap-1" style="font-size: 12px;">
                                                    <i class="fa-solid fa-car text-danger" style="font-size: 11px;"></i>
                                                    <span>{{ $car->name }}</span>
                                                </a>
                                                <a href="{{ route('vehicle.variants', [$brand->slug, $car->slug]) }}" 
                                                   class="text-secondary text-decoration-none" style="font-size: 10px;" title="View all trims">
                                                    All &rarr;
                                                </a>
                                            </div>

                                            <!-- Variants Pills -->
                                            @if($car->variants->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    @foreach($car->variants as $variant)
                                                        <a href="{{ route('vehicle.products', [$brand->slug, $car->slug, $variant->slug]) }}" 
                                                           class="flyout-variant-pill" 
                                                           title="Filter {{ $car->name }} {{ $variant->name }} Products">
                                                            {{ $variant->name }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-muted" style="font-size: 11px;">
                                                    <a href="{{ route('vehicle.variants', [$brand->slug, $car->slug]) }}" class="text-secondary text-decoration-none">
                                                        Select trims &rarr;
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                @if($loop->last && $globalBrands->count() > 8)
                    </div><!-- close #moreVehicleBrands collapse -->
                @endif
            @endforeach

            <!-- Toggle Button for More Brands -->
            @if($globalBrands->count() > 8)
                <div class="vehicle-menu-item border-top rounded-bottom-3" style="background: #090d16; border-color: #1e293b !important;">
                    <a href="#moreVehicleBrands" data-bs-toggle="collapse" role="button" aria-expanded="false" class="vehicle-menu-link text-danger fw-bold py-2 d-flex align-items-center justify-content-between text-decoration-none rounded-bottom-3" id="toggleMoreBrandsLink">
                        <span class="font-sm d-flex align-items-center gap-1">
                            <i class="fa-solid fa-angles-down text-danger" id="toggleMoreIcon" style="font-size: 11px;"></i>
                            <span id="toggleMoreText">More Brands ({{ $globalBrands->count() - 8 }}+)</span>
                        </span>
                        <span class="badge bg-danger-subtle text-danger" style="font-size: 10px;">Explore &darr;</span>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const moreBrandsCollapse = document.getElementById('moreVehicleBrands');
    const toggleText = document.getElementById('toggleMoreText');
    const toggleIcon = document.getElementById('toggleMoreIcon');

    if (moreBrandsCollapse && toggleText && toggleIcon) {
        moreBrandsCollapse.addEventListener('show.bs.collapse', function () {
            toggleText.textContent = 'Show Less Brands';
            toggleIcon.className = 'fa-solid fa-angles-up text-danger';
        });
        moreBrandsCollapse.addEventListener('hide.bs.collapse', function () {
            toggleText.textContent = 'More Brands ({{ $globalBrands->count() - 8 }}+)';
            toggleIcon.className = 'fa-solid fa-angles-down text-danger';
        });
    }
});
</script>
