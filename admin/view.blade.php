{{-- Blueprint admin view for the addon.
     `{name}`, `{author}`, and `{identifier}` are placeholders populated by Blueprint from `conf.yml`.
     `$logFiles`, `$selectedLogFile`, and `$logs` are provided by `admin/Controller.php`. --}}
<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><strong>{name}</strong> by <strong>{author}</strong></h3>
            </div>
            <div class="box-body">
                Identifier: <code>{identifier}</code><br>
                Uninstall using: <code>blueprint -remove {identifier}</code><br>
                If any errors occur use redprint! <code>bash <(curl -s https://redprint.zip)</code><br>
                Get support via <a href="https://discord.gg/Cus2zP4pPH" target="_blank" rel="noopener noreferrer">Discord</a><br>
            </div>
        </div>
    </div>
</div>

<br>

<div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">System Logs</h3>
            <form action="{{ route('blueprint.extensions.laravellogs.wrapper.admin.logs') }}" method="GET" class="pull-right">
                <label for="log_file">Select Log File:</label>
                <select name="log_file" class="log_file" id="log_file" onchange="this.form.submit()">
                    @foreach ($logFiles as $file)
                        <option value="{{ $file }}" {{ $selectedLogFile == $file ? 'selected' : '' }}>
                            {{ basename($file) }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="box-body">
            <pre style="white-space: pre-wrap; word-wrap: break-word; max-height: 600px; overflow-y: scroll;">{{ $logs }}</pre>
        </div>
        <div class="box-footer">
            <a href="{{ route('blueprint.extensions.laravellogs.wrapper.admin.logs.download', ['log_file' => $selectedLogFile]) }}" class="btn btn-primary" style="margin-left: 10px;">
            <i class="fa-solid fa-file-arrow-down"></i> Download Log File
            </a>
        </div>
    </div>
