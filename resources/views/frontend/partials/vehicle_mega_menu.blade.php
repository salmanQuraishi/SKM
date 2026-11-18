<div class="vehicle-mega-menu-wrapper">
    <div class="vehicle-mega-menu shadow-sm rounded-3 border" style="background: #111827; border-color: #1e293b !important;">
        <div class="p-2 px-3 border-bottom d-flex align-items-center justify-content-between rounded-top-3" style="background: #090d16; border-color: #1e293b !important;">
            <span class="text-uppercase fw-bold text-secondary font-sm" style="font-size: 11px; letter-spacing: 0.5px;">
                <i class="fa-solid fa-car-side text-danger me-1"></i> Popular Car Brands
            </span>
            <a href="{{ route('vehicles') }}" class="text-danger fw-bold font-sm text-decoration-none" style="font-size: 11px;">All ({{ $globalBrands->count() }}) &rarr;</a>
        </div>
        <div class="vehicle-menu-list position-relative">
            @foreach($globalBrands as $brand)
                @php
                    $modelsCount = $brand->car_models_count ?? ($brand->relationLoaded('carModels') ? $brand->carModels->count() : 0);
                @endphp
                @if($loop->iteration == 9)<div class="collapse" id="moreVehicleBrands">@endif
                <div class="vehicle-menu-item" data-brand-slug="{{ $brand->slug }}" style="border-bottom: 1px solid #1e293b;">
                    <a href="{{ route('vehicle.cars', $brand->slug) }}" class="vehicle-menu-link">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}" class="brand-icon-img" loading="lazy" decoding="async" style="width: 20px; height: 20px; object-fit: contain;">
                            <span class="brand-name font-sm fw-semibold text-white">{{ $brand->name }}</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            @if($modelsCount > 0)<span class="badge bg-dark text-secondary border border-secondary border-opacity-25 font-sm" style="font-size: 10px;">{{ $modelsCount }}</span>@endif
                            <i class="fa-solid fa-chevron-right text-secondary arrow-icon" style="font-size: 10px;"></i>
                        </div>
                    </a>
                    @if($modelsCount > 0)
                        <div class="vehicle-flyout-pane shadow-lg" data-flyout-slug="{{ $brand->slug }}" data-brand-name="{{ $brand->name }}" data-brand-logo="{{ $brand->logo_url }}" data-brand-url="{{ route('vehicle.cars', $brand->slug) }}" data-loaded="false" style="background: #111827; border: 1px solid #1e293b; border-left: 3px solid var(--primary-red);"></div>
                    @endif
                </div>
                @if($loop->last && $globalBrands->count() > 8)</div>@endif
            @endforeach

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

    window.__brandHierarchyCache = window.__brandHierarchyCache || {};
    const pendingRequests = {};

    function escHtml(str) {
        const div = document.createElement('div');
        div.textContent = str == null ? '' : String(str);
        return div.innerHTML;
    }

    function renderFlyoutHeader(name, logoUrl, carsUrl) {
        return '<div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom" style="border-color: #1e293b !important;">'
            + '<div class="d-flex align-items-center gap-2">'
            + '<img src="' + escHtml(logoUrl) + '" alt="' + escHtml(name) + '" loading="lazy" decoding="async" style="height: 24px; width: 36px; object-fit: contain;">'
            + '<h6 class="fw-bold mb-0 text-white brand-font text-uppercase" style="font-size: 14px;">' + escHtml(name) + ' Cars &amp; Variants</h6>'
            + '</div>'
            + '<a href="' + escHtml(carsUrl) + '" class="text-danger fw-bold text-decoration-none font-sm" style="font-size: 12px;">All ' + escHtml(name) + ' Models &rarr;</a>'
            + '</div>';
    }

    function renderFlyoutModels(models) {
        if (!models || models.length === 0) {
            return '<div class="text-secondary small py-3 text-center">No active models found.</div>';
        }
        let html = '<div class="row row-cols-1 row-cols-md-2 g-2">';
        models.forEach(function (car) {
            html += '<div class="col"><div class="flyout-car-box p-2 rounded border" style="background: #161f30; border-color: #1e293b !important;">';
            html += '<div class="d-flex align-items-center justify-content-between mb-1">';
            html += '<a href="' + escHtml(car.url) + '" class="fw-bold text-white text-decoration-none font-sm hover-red d-flex align-items-center gap-1" style="font-size: 12px;"><i class="fa-solid fa-car text-danger" style="font-size: 11px;"></i><span>' + escHtml(car.name) + '</span></a>';
            html += '<a href="' + escHtml(car.url) + '" class="text-secondary text-decoration-none" style="font-size: 10px;" title="View all trims">All &rarr;</a>';
            html += '</div>';
            if (car.variants && car.variants.length > 0) {
                html += '<div class="d-flex flex-wrap gap-1 mt-1">';
                car.variants.forEach(function (variant) {
                    html += '<a href="' + escHtml(variant.url) + '" class="flyout-variant-pill" title="Filter ' + escHtml(car.name) + ' ' + escHtml(variant.name) + ' Products">' + escHtml(variant.name) + '</a>';
                });
                html += '</div>';
            } else {
                html += '<div class="text-muted" style="font-size: 11px;"><a href="' + escHtml(car.url) + '" class="text-secondary text-decoration-none">Select trims &rarr;</a></div>';
            }
            html += '</div></div>';
        });
        html += '</div>';
        return html;
    }

    function loadBrandFlyout(slug, pane) {
        if (!slug || !pane || pane.dataset.loaded === 'true') return;
        const name = pane.getAttribute('data-brand-name') || slug;
        const logoUrl = pane.getAttribute('data-brand-logo') || '';
        const carsUrl = pane.getAttribute('data-brand-url') || ('/vehicle/' + encodeURIComponent(slug));

        if (window.__brandHierarchyCache[slug]) {
            pane.innerHTML = renderFlyoutHeader(name, logoUrl, carsUrl) + renderFlyoutModels(window.__brandHierarchyCache[slug].models);
            pane.dataset.loaded = 'true';
            return;
        }

        pane.innerHTML = renderFlyoutHeader(name, logoUrl, carsUrl)
            + '<div class="text-center py-4 text-secondary font-sm"><i class="fa-solid fa-spinner fa-spin text-danger me-2"></i> Loading ' + escHtml(name) + ' models &amp; variants...</div>';

        if (!pendingRequests[slug]) {
            pendingRequests[slug] = fetch('/ajax/brand-hierarchy/' + encodeURIComponent(slug), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            }).then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            });
        }

        pendingRequests[slug].then(function (data) {
            if (data && data.success) {
                window.__brandHierarchyCache[slug] = data;
                pane.innerHTML = renderFlyoutHeader(name, logoUrl, carsUrl) + renderFlyoutModels(data.models);
                pane.dataset.loaded = 'true';
            } else {
                pane.innerHTML = renderFlyoutHeader(name, logoUrl, carsUrl) + '<div class="text-secondary small py-3 text-center">Unable to load models.</div>';
            }
        }).catch(function () {
            delete pendingRequests[slug];
            pane.innerHTML = renderFlyoutHeader(name, logoUrl, carsUrl) + '<div class="text-secondary small py-3 text-center">Unable to load models right now.</div>';
        });
    }

    document.querySelectorAll('.vehicle-menu-item[data-brand-slug]').forEach(function (item) {
        const slug = item.getAttribute('data-brand-slug');
        const pane = item.querySelector('.vehicle-flyout-pane');
        if (!slug || !pane) return;
        const triggerLoad = function () { loadBrandFlyout(slug, pane); };
        item.addEventListener('mouseenter', triggerLoad, { passive: true });
        item.addEventListener('focusin', triggerLoad, { passive: true });
    });
});
</script>
