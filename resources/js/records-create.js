// resources/js/records_create.js
document.addEventListener('DOMContentLoaded', function () {
    const medicationsContainer = document.getElementById('medications-container');
    const addMedicationBtn = document.getElementById('add-medication-btn');
    const timingTagSelect = document.getElementById('timing_tag_id');
    const takenDateInput = document.getElementById('taken_date');
    const takenAtHiddenInput = document.getElementById('taken_at_hidden');

    // 服用しなかった理由の選択肢をJavaScriptで定義
    const reasonOptions = [
        { value: '', text: '理由を選択してください' },
        { value: '飲み忘れ', text: '飲み忘れ' },
        { value: '気分が悪かったため', text: '気分が悪かったため' },
        { value: '副作用が心配なため', 'text': '副作用が心配なため' },
        { value: '医師の指示により中止', text: '医師の指示により中止' },
        { value: 'その他', text: 'その他' }
    ];

    // PHPから渡された薬のリストを取得 (JSON形式)
    const availableMedications = window.medicationsList || [];

    let medicationIndex = medicationsContainer.children.length;
    if (medicationIndex > 0) {
        const lastChild = medicationsContainer.lastElementChild;
        const selectElement = lastChild.querySelector('select[name^="medications"]');
        if (selectElement) {
            const nameAttr = selectElement.getAttribute('name');
            const match = nameAttr.match(/medications\[(\d+)\]/);
            if (match && match[1]) {
                medicationIndex = parseInt(match[1]) + 1;
            }
        }
    }

    // 各薬の完了チェックボックスと理由フィールドの初期設定とイベントリスナー設定
    function setupMedicationEntry(entryElement) {
        const isCompletedCheckbox = entryElement.querySelector('.medication-completed-checkbox');
        const reasonNotTakenField = entryElement.querySelector('.reason-not-taken-field');
        const reasonSelect = reasonNotTakenField.querySelector('.reason-select');

        // ★修正点1: 理由ドロップダウンの既存オプションを全てクリア★
        // reasonSelect.innerHTML = ''; // これでも良いが、より効率的な方法
        while (reasonSelect.firstChild) {
            reasonSelect.removeChild(reasonSelect.firstChild);
        }

        // 理由ドロップダウンにオプションを追加
        reasonOptions.forEach(optionData => {
            const option = document.createElement('option');
            option.value = optionData.value;
            option.textContent = optionData.text;
            reasonSelect.appendChild(option);
        });

        // 薬のドロップダウンにオプションを追加 (こちらも重複しないように修正)
        const medicationSelect = entryElement.querySelector('.medication-select');
        // ★修正点2: 薬ドロップダウンの既存オプションを全てクリア★
        while (medicationSelect.firstChild) {
            medicationSelect.removeChild(medicationSelect.firstChild);
        }
        // デフォルトの「薬を選択」オプションを最初に追加
        const defaultMedicationOption = document.createElement('option');
        defaultMedicationOption.value = '';
        defaultMedicationOption.textContent = '薬を選択';
        medicationSelect.appendChild(defaultMedicationOption);

        availableMedications.forEach(medication => {
            const option = document.createElement('option');
            option.value = medication.medication_id;
            option.textContent = medication.medication_name;
            medicationSelect.appendChild(option);
        });

        // old() データから薬を選択状態にする
        const oldMedicationId = medicationSelect.getAttribute('data-old-medication-id');
        if (oldMedicationId) {
            medicationSelect.value = oldMedicationId;
        }

        // old() データから理由を選択状態にする
        const oldReasonValue = reasonSelect.getAttribute('data-old-reason-value');
        if (oldReasonValue) {
            reasonSelect.value = oldReasonValue;
        }

        // 初期表示状態を設定
        if (isCompletedCheckbox.checked) {
            reasonNotTakenField.style.display = 'none';
            reasonSelect.value = ''; // 完了時は理由をクリア (ただしold値がある場合はそれを優先)
        } else {
            reasonNotTakenField.style.display = 'block';
        }

        // イベントリスナー
        isCompletedCheckbox.addEventListener('change', function() {
            if (this.checked) {
                reasonNotTakenField.style.display = 'none';
                reasonSelect.value = ''; // 完了時は理由をクリア
            } else {
                reasonNotTakenField.style.display = 'block';
            }
        });
    }

    // 既存の薬の入力欄全てに初期設定を適用
    medicationsContainer.querySelectorAll('.medication-entry').forEach(setupMedicationEntry);

    addMedicationBtn.addEventListener('click', function () {
        addMedicationEntry();
    });

    medicationsContainer.addEventListener('click', function (event) {
        const removeButton = event.target.closest('.remove-medication-btn');
        if (removeButton) {
            const entryToRemove = removeButton.closest('.medication-entry');
            if (entryToRemove) {
                entryToRemove.remove();
            }
        }
    });

    // taken_at_hidden の値を更新するロジック
    function updateTakenAtHidden() {
        const selectedDate = takenDateInput.value;
        const selectedOption = timingTagSelect.options[timingTagSelect.selectedIndex];
        const baseTime = selectedOption.dataset.baseTime;

        if (selectedDate && baseTime) {
            const fullDateTime = `${selectedDate}T${baseTime.substring(0, 5)}`;
            takenAtHiddenInput.value = fullDateTime;
        } else if (selectedDate && !baseTime) {
            takenAtHiddenInput.value = `${selectedDate}T00:00`;
        } else {
            takenAtHiddenInput.value = '';
        }
    }

    takenDateInput.addEventListener('change', updateTakenAtHidden);
    timingTagSelect.addEventListener('change', updateTakenAtHidden);
    updateTakenAtHidden(); // 初期ロード時に一度実行

    // 薬の入力欄を追加する関数
    function addMedicationEntry() {
        const newEntry = document.createElement('div');
        newEntry.classList.add('p-4', 'border', 'border-gray-200', 'rounded-md', 'mb-2', 'medication-entry', 'bg-gray-50');

        // 薬のオプションHTMLを生成
        const medicationOptionsHtml = availableMedications.map(medication => `<option value="${medication.medication_id}">${medication.medication_name}</option>`).join('');
        // 理由のオプションHTMLを生成
        const reasonOptionsHtml = reasonOptions.map(option => `<option value="${option.value}">${option.text}</option>`).join('');

        newEntry.innerHTML = `
            <div class="flex items-center space-x-2 mb-2">
                <select name="medications[${medicationIndex}][medication_id]" class="medication-select block w-2/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    <option value="">薬を選択</option>
                    ${medicationOptionsHtml}
                </select>
                <input type="text" name="medications[${medicationIndex}][taken_dosage]"
                       class="w-1/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                       placeholder="服用量">
                <button type="button" class="text-red-600 hover:text-red-800 remove-medication-btn">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>

            <div class="mt-2">
                <input type="checkbox" name="medications[${medicationIndex}][is_completed]" id="is_completed_${medicationIndex}" value="1" checked
                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 medication-completed-checkbox">
                <label for="is_completed_${medicationIndex}" class="ml-2 text-sm text-gray-600">服用完了</label>
            </div>

            <div class="mt-2 reason-not-taken-field" style="display: none;">
                <label for="reason_not_taken_${medicationIndex}" class="block text-sm font-medium text-gray-700">服用しなかった理由</label>
                <select name="medications[${medicationIndex}][reason_not_taken]" id="reason_not_taken_${medicationIndex}"
                        class="reason-select mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">理由を選択してください</option>
                    ${reasonOptionsHtml}
                </select>
            </div>
        `;
        medicationsContainer.appendChild(newEntry);

        // 新しく追加されたエントリにイベントリスナーを設定
        setupMedicationEntry(newEntry);

        medicationIndex++;
    }
});