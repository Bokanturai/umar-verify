<!-- Smart Idea: Comment Response Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
            
            <!-- Modal Header -->
            <div class="modal-header bg-primary text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-chat-left-text fs-20"></i>
                    <h5 class="modal-title text-white mb-0 fw-semibold" id="commentModalLabel">Request Response</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Modal Body -->
            <div class="modal-body bg-light p-4" id="commentModalBody" style="font-size: 1rem; white-space: pre-wrap; min-height: 160px;">
                <div class="text-center text-secondary">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p class="mb-0">Loading comment...</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-white border-top d-flex justify-content-between align-items-center py-2 px-4">
                <div class="d-flex flex-wrap gap-2">
                    <a href="javascript:void(0);" id="downloadBtn" class="btn btn-sm btn-success rounded-pill px-3 d-none" target="_blank" download>
                        <i class="bi bi-download me-1"></i> Download File
                    </a>
                </div>
                <div id="encouragement" class="text-muted small fst-italic"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ENCOURAGEMENT_MESSAGES = [
            "Keep pushing forward — success is near!",
            "Believe in yourself, you've got this!",
            "Every step counts, no matter how small.",
            "Great things take time — stay patient.",
            "Your effort today is your victory tomorrow!",
            "Stay focused — progress is happening.",
            "Never give up — your breakthrough is close!"
        ];

        const elements = {
            modal: document.getElementById("commentModal"),
            modalBody: document.getElementById("commentModalBody"),
            encouragement: document.getElementById("encouragement"),
            downloadBtn: document.getElementById("downloadBtn")
        };

        elements.modal.addEventListener("show.bs.modal", handleModalOpen);

        function handleModalOpen(event) {
            const button = event.relatedTarget;
            const comment = button.getAttribute('data-comment');
            const fileUrl = button.getAttribute('data-file-url');
            const approvedBy = button.getAttribute('data-approved-by');

            populateModalContent(comment, approvedBy);
            handleDownloadButton(fileUrl);
            displayEncouragement();
        }

        function populateModalContent(comment, approvedBy) {
            let content = comment || 'No comment available.';
            if (approvedBy) {
                content += `\n\nApproved By: ${approvedBy}`;
            }
            elements.modalBody.innerText = content;
        }

        function handleDownloadButton(fileUrl) {
            if (fileUrl && fileUrl !== 'null' && fileUrl.trim() !== '') {
                const fileName = fileUrl.split('/').pop();
                elements.downloadBtn.href = fileUrl;
                elements.downloadBtn.setAttribute('download', fileName);
                elements.downloadBtn.classList.remove('d-none');
            } else {
                elements.downloadBtn.classList.add('d-none');
                elements.downloadBtn.href = '#';
            }
        }

        function displayEncouragement() {
            const randomMessage = ENCOURAGEMENT_MESSAGES[Math.floor(Math.random() * ENCOURAGEMENT_MESSAGES.length)];
            elements.encouragement.innerText = randomMessage;
        }
    });
</script>
