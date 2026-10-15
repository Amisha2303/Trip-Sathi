{{--
    Cover-image upload card shared by catalogue forms.
    Expects: $record (Model|null), $label (string, default 'Cover image')
--}}
@php($label ??= 'Cover image')
<div class="ts-card">
    <div class="ts-card-body">
        <div class="ts-card-title mb-3">{{ $label }}</div>

        @if ($record->exists && $record->main_image)
            <div class="mb-3">
                <img src="{{ $record->image_url }}" alt="{{ $record->name ?? 'image' }}"
                    class="img-thumbnail" style="max-height: 140px;">
            </div>
        @endif

        <div class="mb-3">
            <label for="main_image" class="form-label fw-semibold">Upload new image</label>
            <input type="file" class="form-control @error('main_image') is-invalid @enderror"
                id="main_image" name="main_image" accept="image/*">
            <div class="form-text">JPG, PNG or WEBP up to 4 MB.</div>
            @error('main_image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if ($record->exists && $record->main_image)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                <label class="form-check-label text-danger" for="remove_image">Remove current image</label>
            </div>
        @endif
    </div>
</div>
