<div class="select-none">
    <!-- ========== MAIN SIDEBAR ========== -->
    <div id="hs-pro-sidebar"
        class="hs-overlay [--body-scroll:true] lg:[--overlay-backdrop:false] [--is-layout-affect:true] [--opened:lg] [--auto-close:lg]
hs-overlay-open:translate-x-0 lg:hs-overlay-layout-open:translate-x-0
-translate-x-full transition-all duration-300 transform
w-60
hidden
fixed inset-y-0 z-60 start-0
bg-sidebar-2
lg:block lg:-translate-x-full lg:end-auto lg:bottom-0"
        role="dialog" tabindex="-1" aria-label="Sidebar">
        <div class="lg:pt-13 relative flex flex-col h-full max-h-full">
            <!-- Body -->
            <nav
                class="p-3 size-full flex flex-col overflow-y-auto [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-none [&::-webkit-scrollbar-track]:bg-scrollbar-track [&::-webkit-scrollbar-thumb]:bg-scrollbar-thumb">
                <div class="lg:hidden mb-2 flex items-center justify-between">
                    <button type="button"
                        class="flex items-center gap-x-1.5 py-2 px-2.5 font-medium text-xs bg-secondary text-secondary-foreground rounded-lg focus:outline-hidden disabled:opacity-50 disabled:pointer-events-none">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            fill="currentColor" viewBox="0 0 16 16">
                            <path
                                d="M7.657 6.247c.11-.33.576-.33.686 0l.645 1.937a2.89 2.89 0 0 0 1.829 1.828l1.936.645c.33.11.33.576 0 .686l-1.937.645a2.89 2.89 0 0 0-1.828 1.829l-.645 1.936a.361.361 0 0 1-.686 0l-.645-1.937a2.89 2.89 0 0 0-1.828-1.828l-1.937-.645a.361.361 0 0 1 0-.686l1.937-.645a2.89 2.89 0 0 0 1.828-1.828zM3.794 1.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387A1.73 1.73 0 0 0 4.593 5.69l-.387 1.162a.217.217 0 0 1-.412 0L3.407 5.69A1.73 1.73 0 0 0 2.31 4.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387A1.73 1.73 0 0 0 3.407 2.31zM10.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.16 1.16 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.16 1.16 0 0 0-.732-.732L9.1 2.137a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732z" />
                        </svg>
                        Ask AI
                    </button>
                    <button type="button"
                        class="p-1.5 size-7.5 inline-flex items-center gap-x-1 text-xs rounded-md text-muted-foreground-1 disabled:opacity-50 disabled:pointer-events-none focus:outline-hidden"
                        aria-haspopup="dialog" aria-expanded="false" aria-controls="hs-pro-sidebar"
                        data-hs-overlay="#hs-pro-sidebar">
                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                        <span class="sr-only">Sidebar Toggle</span>
                    </button>
                </div>

                {{-- Home --}}
                <div
                    class="pt-3 mt-3 flex flex-col border-t border-sidebar-2-divider first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2.5 mb-2 font-medium text-xs uppercase text-muted-foreground-1">
                        Home
                    </span>
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-sidebar-2-nav-foreground rounded-lg hover:bg-sidebar-2-nav-hover focus:outline-hidden focus:bg-sidebar-2-nav-focus"
                                href="/admin/dashboard">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="7" height="7" />
                                    <rect x="14" y="3" width="7" height="7" />
                                    <rect x="14" y="14" width="7" height="7" />
                                    <rect x="3" y="14" width="7" height="7" />
                                </svg>
                                Dashboard
                            </a>
                        </li>
                    </ul>
                </div>
                {{-- Manage Request (Coordinator) --}}
                <div
                    class="pt-3 mt-3 flex flex-col border-t border-sidebar-2-divider first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2.5 mb-2 font-medium text-xs uppercase text-muted-foreground-1">
                        Manage Request
                    </span>
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-sidebar-2-nav-foreground rounded-lg hover:bg-sidebar-2-nav-hover focus:outline-hidden focus:bg-sidebar-2-nav-focus"
                                href="/admin/manage-coordinator">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10 9 9 9 8 9" />
                                </svg>
                                Coordinator Requests
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Management --}}
                <div
                    class="pt-3 mt-3 flex flex-col border-t border-sidebar-2-divider first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2.5 mb-2 font-medium text-xs uppercase text-muted-foreground-1">
                        Management
                    </span>
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-sidebar-2-nav-foreground rounded-lg hover:bg-sidebar-2-nav-hover focus:outline-hidden focus:bg-sidebar-2-nav-focus"
                                href="/admin/users">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                                Users
                            </a>
                        </li>
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-sidebar-2-nav-foreground rounded-lg hover:bg-sidebar-2-nav-hover focus:outline-hidden focus:bg-sidebar-2-nav-focus"
                                href="/admin/students">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                                    <path d="M6 12v5c3 3 9 3 12 0v-5" />
                                </svg>
                                Registered
                            </a>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-sidebar-2-nav-foreground rounded-lg hover:bg-sidebar-2-nav-hover focus:outline-hidden focus:bg-sidebar-2-nav-focus"
                                href="/admin/departments">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                    class="size-4">
                                    <path
                                        d="M7.702 1.368a.75.75 0 0 1 .597 0c2.098.91 4.105 1.99 6.004 3.223a.75.75 0 0 1-.194 1.348A34.27 34.27 0 0 0 8.341 8.25a.75.75 0 0 1-.682 0c-.625-.32-1.262-.62-1.909-.901v-.542a36.878 36.878 0 0 1 2.568-1.33.75.75 0 0 0-.636-1.357 38.39 38.39 0 0 0-3.06 1.605.75.75 0 0 0-.372.648v.365c-.773-.294-1.56-.56-2.359-.8a.75.75 0 0 1-.194-1.347 40.901 40.901 0 0 1 6.005-3.223ZM4.25 8.348c-.53-.212-1.067-.411-1.611-.596a40.973 40.973 0 0 0-.418 2.97.75.75 0 0 0 .474.776c.175.068.35.138.524.21a5.544 5.544 0 0 1-.58.681.75.75 0 1 0 1.06 1.06c.35-.349.655-.726.915-1.124a29.282 29.282 0 0 0-1.395-.617A5.483 5.483 0 0 0 4.25 8.5v-.152Z" />
                                    <path
                                        d="M7.603 13.96c-.96-.6-1.958-1.147-2.989-1.635a6.981 6.981 0 0 0 1.12-3.341c.419.192.834.393 1.244.602a2.25 2.25 0 0 0 2.045 0 32.787 32.787 0 0 1 4.338-1.834c.175.978.315 1.969.419 2.97a.75.75 0 0 1-.474.776 29.385 29.385 0 0 0-4.909 2.461.75.75 0 0 1-.794 0Z" />
                                </svg>
                                Departments
                            </a>
                        </li>
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-sidebar-2-nav-foreground rounded-lg hover:bg-sidebar-2-nav-hover focus:outline-hidden focus:bg-sidebar-2-nav-focus"
                                href="/admin/roles">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                </svg>
                                Roles & Permissions
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- System --}}
                <div
                    class="pt-3 mt-3 flex flex-col border-t border-sidebar-2-divider first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2.5 mb-2 font-medium text-xs uppercase text-muted-foreground-1">
                        System
                    </span>
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-sidebar-2-nav-foreground rounded-lg hover:bg-sidebar-2-nav-hover focus:outline-hidden focus:bg-sidebar-2-nav-focus"
                                href="/admin/audit-logs">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    <polyline points="9 12 11 14 15 10" />
                                </svg>
                                Audit Logs
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Mobile Others --}}
                <div
                    class="pt-3 mt-3 lg:hidden flex flex-col border-t border-sidebar-2-divider first:border-t-0 first:pt-0 first:mt-0">
                    <span class="block ps-2.5 mb-2 font-medium text-xs uppercase text-muted-foreground-1">
                        Others
                    </span>
                    <ul class="flex flex-col gap-y-1">
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-sidebar-2-nav-foreground rounded-lg hover:bg-sidebar-2-nav-hover focus:outline-hidden focus:bg-sidebar-2-nav-focus"
                                href="#">
                                Docs
                            </a>
                        </li>
                        <li>
                            <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-sidebar-2-nav-foreground rounded-lg hover:bg-sidebar-2-nav-hover focus:outline-hidden focus:bg-sidebar-2-nav-focus"
                                href="#">
                                API
                            </a>
                        </li>
                    </ul>
                </div>

            </nav>
            <!-- End Body -->

            <!-- Footer -->
            {{-- <footer class="mt-auto p-3 flex flex-col">
      <ul class="flex flex-col gap-y-1">
        <li>
          <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-sidebar-2-nav-foreground rounded-lg hover:bg-sidebar-2-nav-hover focus:outline-hidden focus:bg-sidebar-2-nav-focus" href="#">
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
            What's new?
          </a>
        </li>
        <li>
          <a class="w-full flex items-center gap-x-2 py-2 px-2.5 text-sm text-sidebar-2-nav-foreground rounded-lg hover:bg-sidebar-2-nav-hover focus:outline-hidden focus:bg-sidebar-2-nav-focus" href="#">
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
            Help & support
          </a>
        </li>
      </ul>
    </footer> --}}
            <!-- End Footer -->
        </div>
    </div>
    <!-- End Sidebar -->
    <!-- ========== END MAIN SIDEBAR ========== -->
</div>
