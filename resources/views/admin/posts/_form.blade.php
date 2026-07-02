@php($isEdit = $post && $post->exists)
<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Titulo *</label>
            <input type="text" name="title" class="form-control form-control-sm"
                value="{{ old('title', $post->title) }}" required maxlength="50">
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Conteudo *</label>
            <input type="text" name="content" class="form-control form-control-sm"
                value="{{ old('content', $post->content) }}" required maxlength="255">
        </div>
    </div>  

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">link *</label>
            <input type="text" name="link" class="form-control form-control-sm"
                value="{{ old('link', $post->link) }}" required maxlength="255">
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">Número de Telefone *</label>
            <input type="text" name="phone" class="form-control form-control-sm"
                value="{{ old('phone', $post->phone) }}" required maxlength="9">
        </div>
    </div> 

    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label text-xs mb-1">	Email *</label>
            <input type="text" name="email" class="form-control form-control-sm"
                value="{{ old('email', $post->email) }}" required maxlength="100">
        </div>
    </div>
    @if(!$isEdit)
        <div class="col-md-4">
            <div class="mb-3">
                <div class="form-check">
                    <input type="hidden" name="feature" value="0">
                    <input class="form-check-input" type="checkbox" value="1" id="feature" name="feature" {{ old('feature', $post->feature) ? 'checked' : '' }}>
                    <label class="form-check-label" for="feature">Ativo</label>
                </div>
            </div>
        </div> 
    @endif
    <div class="col-md-6">
        <div class="mb-3 fileinput-stacked">
            <label class="form-label text-xs mb-1">Imagem</label>
                        <input id="image_file" type="file" name="image_file" class="file fileinput-auto" accept="image/*" data-browse-on-zone-click="true" data-initial-preview-as-data="true"
                                     @if($isEdit && $post->image)
                                         data-initial-preview="{{ asset('img/posts/' . ltrim($post->image, '/')) }}"
                                         data-initial-caption="{{ ltrim($post->image, '/') }}"
                                         data-initial-preview-config='[{"caption":"{{ ltrim($post->image, '/') }}","key":1}]'
                                     @endif
                   data-max-file-size="2048"
                   data-extensions="jpg,jpeg,png,gif,webp,svg"
                   data-browse-label="Escolher" data-remove-label="Limpar" data-msg-placeholder="Selecionar ficheiro…">
        </div>
    </div>
</div>
<div class="d-flex" style="gap:.5rem;">
    <button type="submit" class="btn btn-save btn-sm">Guardar</button>
    <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary btn-sm">Cancelar</a>
</div>






