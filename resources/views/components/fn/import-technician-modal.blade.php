<div
id="importModal"
class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

<div class="bg-white rounded-xl w-full max-w-lg p-8">

<h2 class="text-2xl font-bold mb-6">

📥 Import Data Teknisi

</h2>

<form
action="{{ route('technicians.import') }}"
method="POST"
enctype="multipart/form-data">

@csrf

<input
type="file"
name="file"
required
class="w-full border rounded-lg px-4 py-3 mb-6">

<div class="flex justify-between">

<a
href="{{ route('technicians.template') }}"
class="text-blue-600 hover:underline">

⬇ Download Template

</a>

<div class="space-x-2">

<button
type="button"
onclick="closeImportModal()"
class="px-5 py-2 bg-gray-500 text-white rounded-lg">

Batal

</button>

<button
type="submit"
class="px-5 py-2 bg-green-600 text-white rounded-lg">

Import

</button>

</div>

</div>

</form>

</div>

</div>

<script>

function openImportModal(){

document.getElementById('importModal').classList.remove('hidden');
document.getElementById('importModal').classList.add('flex');

}

function closeImportModal(){

document.getElementById('importModal').classList.remove('flex');
document.getElementById('importModal').classList.add('hidden');

}

</script>