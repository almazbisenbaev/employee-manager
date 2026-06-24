document.addEventListener('DOMContentLoaded', function() {

    // Переменные элементов модалки
    const modal = document.getElementById('em-pdf-modal');
    const listPreviewButton = document.getElementById('em-preview-pdf-button');
    const closeButton = document.getElementById('em-close-pdf-modal');
    const printButton = document.getElementById('em-print-pdf-button');
    const iframe = document.getElementById('em-pdf-iframe');
    const modalTitle = document.getElementById('em-modal-title');
    const modalDownloadLink = document.getElementById('em-modal-download-link');

    // Проверяем, что emData определена (защита от ошибок)
    const listPreviewUrl = typeof emData !== 'undefined' ? emData.previewPdfUrl : '';
    const firstDownloadButton = listPreviewButton?.nextElementSibling;
    const listDownloadUrl = firstDownloadButton?.href || '';
    const textListPreview = typeof emData !== 'undefined' ? emData.textListPreview : 'Предпросмотр списка PDF';
    const textEmployeePreview = typeof emData !== 'undefined' ? emData.textEmployeePreview : 'Предпросмотр сотрудника';

    // Функция открытия модалки
    function openModal(previewUrl, downloadUrl, title) {
        modalTitle.textContent = title;
        modalDownloadLink.href = downloadUrl;
        iframe.src = previewUrl;
        modal.classList.add('em-active');
    }

    // Закрытие модалки
    function closeModal() {
        modal.classList.remove('em-active');
        iframe.src = '';
    }

    // Открыть модалку для списка
    if (listPreviewButton) {
        listPreviewButton.addEventListener('click', function() {
            openModal(listPreviewUrl, listDownloadUrl, textListPreview);
        });
    }

    // Открыть модалку для одного сотрудника
    document.addEventListener('click', function(e) {
        if (e.target.closest('.em-preview-single-pdf')) {
            const button = e.target.closest('.em-preview-single-pdf');
            const previewUrl = button.dataset.previewUrl;
            const downloadUrl = button.dataset.downloadUrl;
            const employeeName = button.dataset.employeeName;
            openModal(previewUrl, downloadUrl, textEmployeePreview + ': ' + employeeName);
        }
    });

    // Закрываем модалку
    if (closeButton) {
        closeButton.addEventListener('click', closeModal);
    }

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Печать
    if (printButton) {
        printButton.addEventListener('click', function() {
            if (iframe.contentWindow) {
                iframe.contentWindow.print();
            }
        });
    }

});
