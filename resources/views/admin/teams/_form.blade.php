@php($isEdit = $team && $team->exists)
<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Name *</label>
            <input type="text" name="name" class="form-control form-control-sm"
                value="{{ old('name', $team->name) }}" required maxlength="50">
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Descrição *</label>
            <input type="text" name="descricao" class="form-control form-control-sm"
                value="{{ old('description', $team->description) }}" required maxlength="255">
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3 fileinput-stacked">
            <label class="form-label text-xs mb-1">Imagem</label>
                        <input id="image_file" type="file" name="image_file" class="file fileinput-auto" accept="image/*" data-browse-on-zone-click="true" data-initial-preview-as-data="true"
                                     @if($isEdit && $team->image)
                                         data-initial-preview="{{ asset('img/teams/' . ltrim($team->image, '/')) }}"
                                         data-initial-caption="{{ ltrim($team->image, '/') }}"
                                         data-initial-preview-config='[{"caption":"{{ ltrim($team->image, '/') }}","key":1}]'
                                     @endif
                   data-max-file-size="2048"
                   data-extensions="jpg,jpeg,png,gif,webp,svg"
                   data-browse-label="Escolher" data-remove-label="Limpar" data-msg-placeholder="Selecionar ficheiro…">
        </div>
    </div>
</div>
<div class="d-flex" style="gap:.5rem;">
    <button type="submit" class="btn btn-save btn-sm">Guardar</button>
    <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary btn-sm">Cancelar</a>
</div>






