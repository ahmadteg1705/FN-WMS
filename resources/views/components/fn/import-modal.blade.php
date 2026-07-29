<div id="importModal"
     class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

    <div class="bg-white rounded-2xl shadow-xl w-[600px]">

        <div class="border-b px-6 py-4">

            <h2 class="text-2xl font-bold">

                📥 Import Data

            </h2>

        </div>

        <form
            action="{{ route('odps.import') }}"
            method="POST"
            enctype="multipart/form-data">

            @csrf

            <div class="p-6">

                <label class="block mb-2 font-semibold">

                    Pilih File Excel

                </label>

                <input
                    type="file"
                    name="file"
                    accept=".xlsx,.xls,.csv"
                    class="w-full border rounded-lg p-3">

                <div class="mt-6">

                    <a href="{{ route('odps.template') }}"
                       class="text-blue-600 hover:underline">

                        📄 Download Template.xlsx

                    </a>

                </div>

            </div>

            <div class="border-t px-6 py-4 flex justify-end gap-3">

                <button
                    type="button"
                    onclick="closeImportModal()"
                    class="bg-gray-300 hover:bg-gray-400 px-5 py-2 rounded-lg">

                    Batal

                </button>

                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">

                    Import

                </button>

            </div>

        </form>

    </div>

</div>