    <div class="modal fade" id="createGradeLevelModal" tabindex="-1" aria-labelledby="createGradeLevelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-header" id="createGradeLevelLabel">Add Grade Level</h5>
                    <button type="button" class="btn-close" data-bs-dismis="modal" aria-label="Close"></button>
                </div>
                <form action="../../../app/controllers/admin/GradeLevelsController.php" method="post">
                    <div class="modal-body">
                        <!-- implement fields here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary">Close</button>
                        <button 
                            type="submit" 
                            class="btn btn-primary" name="create_grade_level"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>