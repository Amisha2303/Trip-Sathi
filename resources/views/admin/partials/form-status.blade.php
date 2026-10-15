{{--
    Publishing toggles card shared by every catalogue form.
    Expects: $record (Model|null), $featured (bool, default true)
--}}
@php($featured ??= true)
<div class="ts-card">
    <div class="ts-card-body">
        <div class="ts-card-title mb-3">Visibility</div>

        <div class="form-check form-switch mb-2">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                @checked(old('is_active', $record->exists ? $record->is_active : true)>
            <label class="form-check-label" for="is_active">Active <span class="text-muted small">(visible on the site)</span></label>
        </div>

        @if ($featured)
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                    @checked(old('is_featured', $record->exists ? $record->is_featured : false))>
                <label class="form-check-label" for="is_featured">Featured <span class="text-muted small">(highlighted on the home page)</span></label>
            </div>
        @endif
    </div>
</div>
