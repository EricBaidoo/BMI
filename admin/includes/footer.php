<?php
// admin/includes/footer.php
?>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-4 px-4 lg:px-8 flex-shrink-0 text-center sm:text-left flex flex-col sm:flex-row justify-between items-center text-sm text-slate-500">
            <p>&copy; <?php echo date('Y'); ?> Bridge Ministries International.</p>
            <p class="mt-1 sm:mt-0">Admin Dashboard System</p>
        </footer>

    </div>

    <!-- Interactive Scripts -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }
    </script>
</body>
</html>
