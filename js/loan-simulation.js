document.addEventListener('DOMContentLoaded', function () {
    // モーダルを開くボタンのクリックイベント
    document.querySelectorAll('.loan-cta-button').forEach(function(button) {
        button.addEventListener('click', function() {
            var uniqId = button.getAttribute('data-loan-id');
            var modal = document.getElementById('modalLoanUnique_' + uniqId);
            modal.style.display = 'block';
        });
    });

    // モーダルを閉じるボタンのクリックイベント
    document.querySelectorAll('.close-modal').forEach(function(closeButton) {
        closeButton.addEventListener('click', function() {
            var uniqId = closeButton.id.replace('closeModalLoanUnique_', '');
            var modal = document.getElementById('modalLoanUnique_' + uniqId);
            modal.style.display = 'none';
        });
    });

        // モーダルの外側をクリックした場合にモーダルを閉じる
    document.querySelectorAll('.modal-loan').forEach(function(modal) {
        modal.addEventListener('click', function(event) {
            // モーダルコンテンツをクリックした場合は閉じない
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    // 入力フィールドの単位を表示する関数
    function updateInputUnit(inputElement) {
        var unit = inputElement.getAttribute('data-unit');
        var value = inputElement.value.replace(/[^\d.]/g, ''); // 数字と小数点以外を削除
        
        // 最大6桁までに制限
        if (value.length > 6) {
            value = value.slice(0, 6); // 先頭から6桁まで切り取る
        }
        
        inputElement.value = value + unit;
    }

    // 各入力フィールドに対してイベントリスナーを設定
    document.querySelectorAll('.loan').forEach(function(input) {
        var unit = input.getAttribute('data-unit');
        if (unit) {
            input.addEventListener('input', function() {
                updateInputUnit(input);
                runCalculation(input); // 入力が変更されるたびに計算を実行
            });
            updateInputUnit(input); // 初期値を設定
        }
    });

    // 「+」ボタンのクリックイベント
    document.querySelectorAll('.plus').forEach(function(button) {
        button.addEventListener('click', function() {
            var targetId = button.getAttribute('data-target');
            var targetInput = document.querySelector(targetId);
            var value = parseFloat(targetInput.value.replace(/[^\d.]/g, ''));
            
            if (!isNaN(value)) {
                if (targetInput.getAttribute('id').includes('rate')) {
                    targetInput.value = (value + 0.01).toFixed(2); // 金利の場合
                } else {
                    targetInput.value = value + 1; // その他の数値の場合
                }
                updateInputUnit(targetInput);
                runCalculation(targetInput); // 計算を実行
            }
        });
    });

    // 「-」ボタンのクリックイベント
    document.querySelectorAll('.minus').forEach(function(button) {
        button.addEventListener('click', function() {
            var targetId = button.getAttribute('data-target');
            var targetInput = document.querySelector(targetId);
            var value = parseFloat(targetInput.value.replace(/[^\d.]/g, ''));
            
            if (!isNaN(value) && value > 0) {
                if (targetInput.getAttribute('id').includes('rate')) {
                    targetInput.value = (value - 0.01).toFixed(2); // 金利の場合
                } else {
                    targetInput.value = value - 1; // その他の数値の場合
                }
                updateInputUnit(targetInput);
                runCalculation(targetInput); // 計算を実行
            }
        });
    });

    // ラジオボタンの変更時に計算を実行
    document.querySelectorAll('input[name^="loanMethod_"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            runCalculation(radio); // 返済方法が変更された際に再計算を実行
        });
    });

    // 計算を実行する関数
    function runCalculation(changedElement) {
        var uniqId = changedElement.closest('.modal-loan').id.replace('modalLoanUnique_', '');
        var method = document.querySelector('input[name="loanMethod_' + uniqId + '"]:checked');
        if (!method) return;

        method = method.value;
        var borrow = document.querySelector('#borrow_' + uniqId);
        var period = document.querySelector('#period_' + uniqId);
        var rate = document.querySelector('#rate_' + uniqId);
        var downPayment = document.querySelector('#downPayment_' + uniqId);
        var bonus = document.querySelector('#bonusAmount_' + uniqId);  // ボーナス額

        // 各項目の値を取得
        var borrowAmount = parseFloat(borrow.value.replace(/[^\d.]/g, '')) * 10000;
        var loanTerm = parseInt(period.value);
        var interestRate = parseFloat(rate.value) / 100;
        var downPaymentAmount = parseFloat(downPayment.value.replace(/[^\d.]/g, '')) * 10000;
        var bonusAmount = parseFloat(bonus.value.replace(/[^\d.]/g, '')) * 10000; // ボーナス額を使用

        // ボーナス未入力時に0を代入
        if (isNaN(bonusAmount)) {
            bonusAmount = 0;
        }

        // ボーナス込みの借入額を計算（借入額 - ボーナス額）
        var adjustedBorrowAmount = borrowAmount - bonusAmount;

        // 計算方法に応じて関数を呼び出し
        if (method === "equalPayment") {
            calculateEqualPaymentLoan(adjustedBorrowAmount, loanTerm, interestRate, downPaymentAmount, bonusAmount, uniqId);
        } else if (method === "equalPrincipal") {
            calculateEqualPrincipalLoan(adjustedBorrowAmount, loanTerm, interestRate, downPaymentAmount, bonusAmount, uniqId);
        }
    }

    // 元利均等返済の計算式
    function calculateEqualPaymentLoan(borrowAmount, loanTerm, interestRate, downPaymentAmount, bonusAmount, uniqId) {
        var principal = borrowAmount - downPaymentAmount; // 頭金を引いた借入額
        var monthlyRate = interestRate / 12; // 月利
        var months = loanTerm * 12; // 返済回数（月数）

        if (principal <= 0) {
            document.querySelector('#monthPay_' + uniqId).value = formatCurrency(0);
            document.querySelector('#monthPayWithBonus_' + uniqId).value = formatCurrency(0);
            return; // 借入額が0以下の場合、返済額を0とする
        }

        // 元利均等返済額計算
        var monthlyPayment = principal * monthlyRate / (1 - Math.pow(1 + monthlyRate, -months));

        // ボーナスを年2回に分けて返済額に加算
        var bonusPaymentPerYear = bonusAmount / 2;

        // ボーナス込みの月々の支払い額（ボーナス額を2回で割って月々に加算）
        var monthlyPaymentWithBonus = monthlyPayment + (bonusPaymentPerYear / 6); // ボーナス額を月に換算して加算

        // 結果を表示
        document.querySelector('#monthPay_' + uniqId).value = formatCurrency(monthlyPayment); // 通常月の支払い額
        document.querySelector('#monthPayWithBonus_' + uniqId).value = formatCurrency(monthlyPaymentWithBonus); // ボーナス込みの月々の支払い額
    }

    // 元金均等返済の計算式
    function calculateEqualPrincipalLoan(borrowAmount, loanTerm, interestRate, downPaymentAmount, bonusAmount, uniqId) {
        var principal = borrowAmount - downPaymentAmount; // 頭金を引いた借入額
        var monthlyRate = interestRate / 12; // 月利
        var months = loanTerm * 12; // 返済回数（月数）

        if (principal <= 0) {
            document.querySelector('#monthPay_' + uniqId).value = formatCurrency(0);
            document.querySelector('#monthPayWithBonus_' + uniqId).value = formatCurrency(0);
            return; // 借入額が0以下の場合、返済額を0とする
        }

        // 毎月の元金部分の計算
        var monthlyPrincipalPayment = principal / months;

        // 初回の利息部分の計算
        var interestPayment = principal * monthlyRate;

        // 初回月の支払い額
        var firstMonthPayment = monthlyPrincipalPayment + interestPayment;

        // ボーナスを年2回に分けて返済額に加算
        var bonusPaymentPerYear = bonusAmount / 2;

        // ボーナス込みの月々の支払い額（ボーナス額を2回で割って月々に加算）
        var monthlyPaymentWithBonus = firstMonthPayment + (bonusPaymentPerYear / 6);

        // 結果を表示
        document.querySelector('#monthPay_' + uniqId).value = formatCurrency(firstMonthPayment); // 初回月の支払い額
        //document.querySelector('#monthPayWithBonus_' + uniqId).value = formatCurrency(monthlyPaymentWithBonus); // ボーナス込みの月々の支払い額
    }

    // 数値を通貨形式にフォーマットする関数
    function formatCurrency(value) {
        return value.toLocaleString('ja-JP', { style: 'currency', currency: 'JPY' });
    }
});
