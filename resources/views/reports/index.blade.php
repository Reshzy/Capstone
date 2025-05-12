<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Available Reports</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Purchase Requests Report Card -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                        <h4 class="font-medium text-lg text-gray-900 mb-2">Purchase Requests</h4>
                        <p class="text-gray-600 mb-4">Generate reports on purchase requests by date range, department, and status.</p>
                        <a href="{{ route('reports.purchase-requests') }}" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            View Report
                        </a>
                    </div>
                    
                    <!-- Budget Approvals Report Card -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                        <h4 class="font-medium text-lg text-gray-900 mb-2">Budget Approvals</h4>
                        <p class="text-gray-600 mb-4">Generate reports on budget approvals by date range, department, and status.</p>
                        <a href="{{ route('reports.budget-approvals') }}" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            View Report
                        </a>
                    </div>
                    
                    <!-- Suppliers Report Card -->
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                        <h4 class="font-medium text-lg text-gray-900 mb-2">Suppliers</h4>
                        <p class="text-gray-600 mb-4">Generate reports on suppliers by category, status, and performance ratings.</p>
                        <a href="{{ route('reports.suppliers') }}" class="text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            View Report
                        </a>
                    </div>
                </div>
                
                <div class="mt-8">
                    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-gray-500">
                        <h4 class="font-medium text-lg text-gray-900 mb-2">System Activity Log</h4>
                        <p class="text-gray-600 mb-4">View a detailed log of all system activities, including user actions and system events.</p>
                        <a href="{{ route('reports.activity-log') }}" class="text-white bg-gray-600 hover:bg-gray-700 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            View Activity Log
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 