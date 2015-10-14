# Accordions

## Markup structure

    <div class="ev-accordion ev-component" role="tablist">
        <div class="ev-toggle">
            <div role="tab" id="ev-accordion-1" class="ev-toggle-trigger" aria-controls="ev-accordion-panel-1"></div>
            <div class="ev-toggle-content" role="tabpanel" aria-labelledby="ev-accordion-1"></div>
        </div>
        <div class="ev-toggle">
            <div role="tab" id="ev-accordion-2" class="ev-toggle-trigger" aria-controls="ev-accordion-panel-2"></div>
            <div class="ev-toggle-content" role="tabpanel" aria-labelledby="ev-accordion-2"></div>
        </div>
        <div class="ev-toggle">
            <div role="tab" id="ev-accordion-3" class="ev-toggle-trigger" aria-controls="ev-accordion-panel-3"></div>
            <div class="ev-toggle-content" role="tabpanel" aria-labelledby="ev-accordion-3"></div>
        </div>
    </div>

## Notes

* A `ev-active` class is applied to toggles in order to trigger their appearance.